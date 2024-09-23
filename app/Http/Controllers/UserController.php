<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{


    public function getUser(Request $request)
    {
        // Load the authenticated user with the 'coeClasses' relationship
        $user = Auth::user()->load('coeClasses'); 
        
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
        $user = User::find($id);
        
        if ($user === null) {
            return response()->json([
                'message' => 'User on this ID is not found'
            ], 404);
        }
    
        $validateUser = Validator::make($request->all(), [
            'first_name' => 'sometimes',
            'last_name' => 'sometimes',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'organization' => 'sometimes',
            'phone_number' => 'sometimes',
            'password' => 'sometimes|min:6',
        ]);
    
        if ($validateUser->fails()) {
            return response()->json([
                'message' => 'Validation is not successfully applied',
                'errors' => $validateUser->errors()
            ], 422);
        }
    
        // Update the user
        $user->update($request->all());
    
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
