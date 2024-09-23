<?php
namespace App\Http\Controllers;

use App\Models\CoeClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Check if the authenticated user is an admin
        if ($request->user()->role->role_name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return User::with(['role', 'coeClasses'])->get();
    }

    public function show(User $user)
    {
        if (auth()->user()->role->role_name !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $user->load('role');
    }

    public function update(Request $request, User $user)
    {
        // Ensure only admins can change roles
        
        $validator = Validator::make($request->all(), [
            'role' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'User role updated successfully']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // get all coe classes
  
}

