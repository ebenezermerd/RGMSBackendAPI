<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::latest()->paginate(10);
        if($messages->count() > 0){
        return MessageResource::collection($messages);
        }else{
            return response()->json([
                'message' => 'No messages found'
                
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Get the authenticated user's ID
    $userId = auth()->id();

        $validateMessage = Validator::make($request->all(), [
            'sender_name' => 'required',
            'message_subject' => 'required',
            'message_content' => 'required',
            'message_date' => 'required',
        ]);
        if ($validateMessage->fails()) {
            return response()->json([ 
                'message' => 'Validation is not successfully applied',
                'errors' => $validateMessage->errors()
            ], 422);}else{
                $messageData = $request->all();
                $messageData['user_id'] = $userId;
                $message = Message::create($messageData);
                return response()->json([
                    'message' => 'Message created successfully',
                    'data' => new MessageResource($message)
                ], 201);
            }
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(string $userid, string $id)
    {   
        $user = User::find($userid);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $message = Message::where('user_id', $userid)->orderBy('created_at', 'desc')->first();
        if ($message) {
            return new MessageResource($message);
        } else {
            return response()->json(['message' => 'Message not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $username, string $id)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message'=> 'User is not found'],404);
        }
        $message = Message::find($id);
        if ($message) {
            $message->delete();
            return response()->json([
                'message' => 'Message deleted successfully',
                'data'=> new MessageResource($message)
            ], 200);}else{
                return response()->json(['message'=> 'Deletion failed'], 404);
            }
    }
}
