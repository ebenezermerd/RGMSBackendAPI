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
        
        return User::with(['role', 'coeClasses', 'proposals'])->get();
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

    public function toggleResearchCallState(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'state' => 'required|boolean',
        ]);

        $admin = User::find($request->admin_id);
        $admin->research_call_state = $request->state;
        $admin->save();

        return response()->json(['message' => 'Research call state updated successfully']);
    }

    public function getCallToggleState(Request $request)
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role_name', 'admin');
        })->first();

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        return response()->json(['state' => $admin->research_call_state]);
    }

    // get all coe classes
  
}

