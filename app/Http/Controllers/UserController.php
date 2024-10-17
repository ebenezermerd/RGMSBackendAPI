<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class   UserController extends Controller
{

    public function index(Request $request)
    {
        // Load the authenticated user with the 'coeClasses' relationship
        $user = Auth::user()->load(['coeClasses', 'role']); 
        
        // Return the user data as a resource
        return response()->json([
            'message' => 'User data',
            'userdata' => new UserResource($user),
            'coeClasses' => $user->coeClasses
        ]);
    }
    /**
     * Show the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('coeClasses')->find($id);
        
        if ($user === null) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        
        $authenticatedUser = Auth::user();
        
        if ($authenticatedUser->id === $user->id) {
            return new UserResource($user);
        } else {
            return response()->json([
                'message' => 'Unauthorized action'
            ], 401);
        }
    }
    //get authenticated user 
    public function getAuthenticatedUser(){
        $user = Auth::user()->load('role');
        return response()->json([
            'message' => 'Authenticated user',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        if($user === null){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }else{
            return response()->json([
                'message'=> 'User to be edited',
                'user' => new UserResource($user)
            ],200);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::with('role')->find($id);
    
        if ($user === null) {
            return response()->json([
                'message' => 'User with this ID is not found'
            ], 404);
        }
    
        // Validate the user input
        $validateUser = Validator::make($request->all(), [
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => "sometimes|email|unique:users,email,{$user->id}",
            'organization' => 'sometimes|string',
            'city' => 'sometimes|string|nullable',
            'bio' => 'sometimes|string|nullable',
            'date_of_birth' => 'sometimes|date',
            'permanent_address' => 'sometimes|string|nullable',
            'present_address' => 'sometimes|string|nullable',
            'profile_image' => 'sometimes|nullable', // Allow both file and URL
            'phone_number' => 'sometimes|string',
        ]);
    
        if ($validateUser->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateUser->errors()
            ], 422);
        }
    
          // Handle profile picture upload
    if ($request->hasFile('profile_image')) {
        // Delete old profile picture if it exists
        if ($user->profile_image) {
            Storage::delete("public/{$user->profile_image}"); // Delete from public storage
        }

        // Get the uploaded file
        $file = $request->file('profile_image');

        // Generate a unique file name with the user's ID and original extension
        $fileName = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Store the file in the 'public/profile_images' directory
        $path = $file->storeAs('profile_images', $fileName, 'public'); // Store in public disk

        // Save the path to the database
        $user->profile_image = $path; // Store relative path
    }
        
    
        // Update other user fields
        $user->update($request->except('profile_image'));
    
        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource($user)
        ], 200);
    }
    

    
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if($user === null){
            return response()->json([
                'message'=> 'User not found',
            ]);}else{
                $user->delete();
                return response()->json([
                    'message'=> 'User deleted successfully',
                    'user'=> new UserResource($user)
                ]);
            }
    }
}
