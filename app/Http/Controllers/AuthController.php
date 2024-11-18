<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\VerificationCodeMail;
use App\Models\Role;
use App\Models\UnregisteredReviewer;
use App\Models\UnregisteredReviewerProposalAssignment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProposalAssignment;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register user
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'message' => 'Validation Error'
            ], 400);
        }
    
        // Find the default role (e.g., 'researcher') in the roles table
        $role = Role::where('role_name', 'researcher')->first();
        // Generate a 6-digit verification code
        $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            // Create the user and associate the default role
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'organization' => $request->organization,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id, // Associate with role
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(60),
        ]);
          // Check if the email exists in unregistered_reviewers
          $unregisteredReviewer = UnregisteredReviewer::where('email', $request->email)->first();

          if ($unregisteredReviewer) {
              // Transfer assignments
              $assignments = UnregisteredReviewerProposalAssignment::where('unregistered_reviewer_id', $unregisteredReviewer->id)->get();
  
              foreach ($assignments as $assignment) {
                  UserProposalAssignment::create([
                      'reviewer_id' => $user->id,
                      'proposal_id' => $assignment->proposal_id,
                      'start_time' => $assignment->start_time,
                      'end_time' => $assignment->end_time,
                      'request_status' => $assignment->request_status,
                      'comment' => $assignment->comment,
                  ]);
  
                  // Delete the unregistered assignment
                  $assignment->delete();
              }
  
              // Delete the unregistered reviewer
              $unregisteredReviewer->delete();
          }

          Mail::to($user->email)->send(new VerificationCodeMail($user));
    
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }
    
    
    // login
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid login credentials'], 401);
    }

    $user = Auth::user()->load('coeClass'); // Load the coeClasses relationship
    $token = $user->createToken('auth_token')->plainTextToken;

    // Include the role name from the roles table
    $role = $user->role->role_name;

    return response()->json([
        'message' => 'Login successful',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'role' => $role, // Send role name to frontend
        'user' => new UserResource($user), // Send user details to frontend
        'email_verified' => $user->hasVerifiedEmail(), // Include email_verified field
    ]);
}

// Logout function
public function logout(Request $request)
{
    // Revoke all tokens for the authenticated user
    $user = $request->user();
    $user->tokens()->delete();

    // Return a successful logout response
    return response()->json(['message' => 'Logged out successfully'], 200);
}


}
