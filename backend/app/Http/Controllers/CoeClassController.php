<?php

// app/Http/Controllers/CoeClassController.php

namespace App\Http\Controllers;

use App\Models\CoeClass;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCoeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoeClassController extends Controller
{
    // Display all COE classes
    public function index()
    {
        $coeClasses = CoeClass::all();
        return response()->json($coeClasses);
    }

    // Create a new COE class
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:coe_classes,name',
            'description' => 'nullable|string',
        ]);

        $coeClass = CoeClass::create($request->all());

        return response()->json(['message' => 'COE class created successfully', 'data' => $coeClass]);
    }

    public function show($id)
    {
        $coeClass = CoeClass::find($id);

        if (!$coeClass) {
            return response()->json(['message' => 'COE class not found'], 404);
        }

        return response()->json($coeClass);
    }

    public function getAllCoeClasses()
    {
        $coeClasses = CoeClass::all();
        return response()->json($coeClasses);
    }

    public function getCoeClass()
    {
        $user = Auth::user();

        if ($user->role->role_name !== 'coe') {
            return response()->json(['error' => 'User is not a COE'], 403);
        }

        $coeClass = $user->coeClasses->first(); // Assuming a user has one COE class
        return response()->json(['coeClass' => $coeClass->name]);
    }
    // lets add here a function that will assign a user to role of coe
    public function changeUserRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:coe,admin,reviewer,researcher',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
          // Check if the role is being changed from 'coe' to another role
          if ($user->role->role_name === 'coe' && $request->role !== 'coe') {
            // Remove COE class relationship
            UserCoeAssignment::where('user_id', $user->id)->delete();
        }

        $user->role_id = match ($request->role) {
            'coe' => 4,
            'reviewer' => 3,
            'admin' => 1,
            default => 2,
        };
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $user
        ]);
    }

    // Assign a COE class to a user
    public function assignUserToCoe(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'coe_class_id' => 'required|exists:coe_classes,id',
        ]);

        // Check if the user already has the coe role
        $user = User::find($request->user_id);

        if ($user->role->role_name !== 'coe') {
            return response()->json(['error' => 'User must have COE role to be assigned'], 400);
        }

        // Create or update the assignment
        UserCoeAssignment::updateOrCreate(
            ['user_id' => $request->user_id],
            ['coe_class_id' => $request->coe_class_id]
        );

        return response()->json(['message' => 'User successfully assigned to COE class']);
    }



    // Show assignments for a specific COE class
    public function showAssignments($coeClassId)
    {
        $coeClass = CoeClass::with('userCoeAssignments.user')->find($coeClassId);

        if (!$coeClass) {
            return response()->json(['message' => 'COE class not found'], 404);
        }

        return response()->json($coeClass->userCoeAssignments);
    }

    
    public function assignReviewer(Request $request, $coeClassName, $proposalId)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'coe_class' => 'required',
        ]);
    
        $user = User::find($request->reviewer_id);
    
        // Ensure the user has the 'reviewer' role
        if ($user->role->role_name !== 'reviewer') {
            return response()->json(['error' => 'User must have reviewer role'], 400);
        }
    
        // Assign the user to the proposal
        $user->proposals()->attach($proposalId);
    
        // Prepare data for insertion
        $sections = ['proposal_title', 'proposal_abstract', 'proposal_introduction', 'proposal_literature', 'proposal_methodology', 'proposal_results', 'proposal_reference'];
        foreach ($sections as $section) {
            DB::table('user_proposal_assignments')->insert([
                'proposal_id' => $proposalId,
                'reviewer_id' => $request->reviewer_id,
                'coe_class' => $request->coe_class,
                'section' => $section,
                'score' => 0,
                'comment' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    
        return response()->json(['message' => 'Reviewer assigned successfully']);
    }


    // Find users assigned to a given proposal
    public function getAssignedReviewers($coeClassId, $proposalId)
    {
        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            return response()->json(['message' => 'Proposal not found'], 404);
        }

        $reviewers = $proposal->users()->whereHas('role', function ($query) {
            $query->where('role_name', 'reviewer');
        })->get();

        return response()->json($reviewers);
    }



}
