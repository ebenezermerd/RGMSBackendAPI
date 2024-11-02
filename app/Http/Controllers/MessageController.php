<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Events\MessageSent; // Import the MessageSent event
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\CoeClass; // Import the CoeClass model

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        $userId = Auth::id();
    
        // Fetch messages where the current user is either the sender or receiver
        $messages = Message::where('sender_id', $userId)
                            ->orWhere('receiver_id', $userId)
                            ->latest()
                            ->paginate(10);
    
        // Check if there are messages
        if ($messages->count() > 0) {
            // Format the messages and return the response
            return response()->json([
            'message' => 'Messages retrieved successfully',
            'data' => $messages->map(function ($message) {
                // Fetch the sender based on their type
                if ($message->sender_type === 'user') {
                $sender = User::find($message->sender_id);
                $senderInfo = $sender ? [
                    'first_name' => $sender->first_name,
                    'last_name' => $sender->last_name,
                    'email' => $sender->email,
                    'name' => '',
                    'profile_image' => $sender->profile_image,
                    'type' => 'User',
                ] : null;
                } elseif ($message->sender_type === 'coe_class') {
                $sender = CoeClass::find($message->sender_id);
                $senderInfo = $sender ? [
                    'first_name' => 'Office Of The COE',
                    'last_name' => '',
                    'email' => 'coe@gmail.com',
                    'profile_image' => null,
                    'name' => $sender->name,
                    'type' => 'COE Class',
                ] : null;
                } elseif ($message->sender_type === 'directorate') {
                $sender = User::where('id', $message->sender_id)->whereHas('roles', function ($query) {
                    $query->where('role_name', 'directorate');
                })->first();
                $senderInfo = $sender ? [
                    'first_name' => 'Directorate',
                    'last_name' => $sender->name,
                    'email' => 'directorate@gmail.com',
                    'profile_image' => null,
                    'name' => $sender->name,
                    'type' => 'Directorate',
                ] : null;
                } else {
                $senderInfo = null;
                }

                // Fetch receiver details
                $receiver = User::find($message->receiver_id);
                $receiverInfo = $receiver ? [
                'first_name' => $receiver->first_name,
                'last_name' => $receiver->last_name,
                'email' => $receiver->email,
                'profile_image' => $receiver->profile_image,
                ] : null;

                // Format the message
                return [
                'sender' => $senderInfo,
                'receiver' => $receiverInfo,
                'message' => new MessageResource($message)
                ];
            })
            ], 200);
        } else {
            return response()->json([
                'message' => 'No messages found'
            ], 204);
        }
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $userId)
    {
        // Find the user by userId
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate the incoming request
        $validateMessage = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message_subject' => 'required|string|max:255',
            'message_content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,pdf,docx|max:25600', // Limit each attachment to 25MB (25600 KB)
        ]);

        if ($validateMessage->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateMessage->errors()
            ], 422);
        }

        // Create a new message instance
        $message = new Message([
            'sender_id' => Auth::id(),
            'receiver_id' => $validateMessage['receiver_id'],
            'message_subject' => $validateMessage['message_subject'],
            'message_content' => $validateMessage['message_content'],
            'attachments' => json_encode($request->attachments ?? []),
            'is_read' => false
        ]);
        $message->save();

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => [
                'sender' => [
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'email' => Auth::user()->email,
                    'profile_image' => Auth::user()->profile_image,
                ],
                'receiver' => [
                    'first_name' => User::find($validateMessage['receiver_id'])->first_name,
                    'last_name' => User::find($validateMessage['receiver_id'])->last_name,
                    'email' => User::find($validateMessage['receiver_id'])->email,
                    'profile_image' => User::find($validateMessage['receiver_id'])->profile_image,
                ],
                'message' => new MessageResource($message)
            ]
        ], 201);
    }

    public function markAsRead($userId, $id)
{
    $user = User::find($userId);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
    
    $message = Message::findOrFail($id);
    $message->is_read = true;
    $message->save();

    return response()->json(['message' => 'Message marked as read'], 200);
}

    /**
     * Display the specified resource.
     */
    public function show(string $userid, string $id)
    {
        $user = User::find($userid);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $message = Message::where('user_id', $userid)->where('id', $id)->first();
        if ($message) {
            return new MessageResource($message);
        } else {
            return response()->json(['message' => 'Message not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $username, string $id)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $message = Message::find($id);
        if ($message) {
            $message->delete();
            return response()->json([
                'message' => 'Message deleted successfully',
                'data' => new MessageResource($message)
            ], 200);
        } else {
            return response()->json(['message' => 'Deletion failed'], 404);
        }
    }

    public function broadcastMessage(Request $request, $userId)
{
    $user = User::find($userId);

    // Ensure the user has COE role before broadcasting
    if (!$user || !$user->hasRole('coe')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Validate message
    $validated = $request->validate([
        'message_subject' => 'required|string|max:255',
        'message_content' => 'required|string',
        'attachments' => 'nullable|array',
        'attachments.*' => 'file|mimes:jpeg,png,pdf,docx',
    ]);

    // Determine recipients (e.g., all researchers under the COE's class)
    $recipients = User::whereHas('proposals', function($query) use ($user) {
        $query->where('coe_id', $user->coe_id); // Ensure only researchers under this COE class receive the message
    })->get();

    // Create message for each recipient
    foreach ($recipients as $recipient) {
        $message = new Message([
            'sender_id' => $user->id,
            'receiver_id' => $recipient->id,
            'message_subject' => $validated['message_subject'],
            'message_content' => $validated['message_content'],
            'is_broadcast' => true,
            'attachments' => json_encode($request->attachments ?? [])
        ]);
        $message->save();
    }

    return response()->json(['message' => 'Broadcast sent successfully'], 201);
}

}
