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

        $coeClass = $user->coeClass->first(); // Assuming a user has one COE class
        return response()->json(['coeClass' => $coeClass->name]);
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
