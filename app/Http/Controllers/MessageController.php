<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Events\MessageSent; // Import the MessageSent event
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::latest()->paginate(10);
        if ($messages->count() > 0) {
            return MessageResource::collection($messages);
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
            'sender_name' => 'required|string|max:255',
            'receiver_email' => 'required|email',
            'message_subject' => 'required|string|max:255',
            'message_content' => 'required|string',
            'message_date' => 'required|date',
        ]);

        if ($validateMessage->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateMessage->errors()
            ], 422);
        }

        // Create a new message instance
        $messageData = $request->all();
        $messageData['user_id'] = $user->id; // Use the found user's ID
        $messageData['profile_image'] = $user->profile_image; // Use the found user's profile image
        $message = Message::create($messageData);

        // Broadcast the message event to others
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message created successfully',
            'data' => new MessageResource($message)
        ], 201);
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
}
