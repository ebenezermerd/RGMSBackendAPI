<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProposalResource;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\StatusAssignment;
use App\Models\Status;
use App\Models\Activity;
use App\Models\Collaborator;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->id) {
            return response()->json([
            'message' => 'Unauthenticated Access',
            ], 401);
        }
        $proposals = Proposal::where('user_id', $user->id)->with('statusAssignments.status')->get();

        if ($proposals->count() > 0) {
            return ProposalResource::collection($proposals);
        } else {
            return response()->json(['message' => 'No proposals found'], 404);
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
        $user_id = auth()->id();

        // Validate the proposal fields and nested phases/activities
        $validateProposal = Validator::make($request->all(), [
            'COE' => 'required',
            'proposal_title' => 'required|unique:proposals',
            'proposal_abstract' => 'required|max:1000',
            'proposal_introduction' => 'required|max:2500',
            'proposal_literature' => 'required|max:2500',
            'proposal_methodology' => 'required|max:1500',
            'proposal_results' => 'required|max:1500',
            'proposal_reference' => 'required|max:1500',
            'proposal_submitted_date' => 'required|date',
            'proposal_end_date' => 'required|date',
            'proposal_budget' => 'required|numeric',
            'collaborators' => 'required|array',
            'collaborators.*.collaborator_name' => 'required|string',
            'collaborators.*.collaborator_gender' => 'required|string',
            'collaborators.*.collaborator_organization' => 'required|string',
            'collaborators.*.collaborator_phone_number' => 'required|string',
            'collaborators.*.collaborator_email' => 'required|email',
            'phases' => 'required|array',
            'phases.*.phase_name' => 'required|string',
            'phases.*.phase_startdate' => 'required|date',
            'phases.*.phase_enddate' => 'required|date',
            'phases.*.phase_objective' => 'required|string',
            'phases.*.activities' => 'required|array',
            'phases.*.activities.*.activity_name' => 'required|string',
            'phases.*.activities.*.activity_budget' => 'required|numeric'
        ]);

        if ($validateProposal->fails()) {
            return response()->json([
                'message' => 'Validation is not successful',
                'errors' => $validateProposal->errors()
            ], 422);
        }

        // Retrieve or create 'pending' status ID
        $pendingStatus = Status::firstOrCreate(['name' => 'pending']);

        // Create the proposal
        $proposalData = $request->all();
        $proposalData['user_id'] = $user_id;
        $proposal = Proposal::create($proposalData);

        // Assign status to the proposal (default to 'pending')
        StatusAssignment::create([
            'status_id' => $request->proposal_status_id ?? $pendingStatus->id,
            'statusable_id' => $proposal->id,
            'statusable_type' => Proposal::class,
        ]);

        // Loop through the phases and create them
        foreach ($request->phases as $phaseData) {
            $phase = Phase::create([
                'phase_name' => $phaseData['phase_name'],
                'phase_startdate' => $phaseData['phase_startdate'],
                'phase_enddate' => $phaseData['phase_enddate'],
                'phase_objective' => $phaseData['phase_objective'],
                'proposal_id' => $proposal->id
            ]);
      // Loop through the collaborators and create them
      foreach ($request->collaborators as $collaboratorData) {
        $collaborator = new Collaborator([
            'collaborator_name' => $collaboratorData['collaborator_name'],
            'collaborator_gender' => $collaboratorData['collaborator_gender'],
            'collaborator_organization' => $collaboratorData['collaborator_organization'],
            'collaborator_phone_number' => $collaboratorData['collaborator_phone_number'],
            'collaborator_email' => $collaboratorData['collaborator_email'],
            'proposal_id' => $proposal->id
        ]);
        $collaborator->save(); // Save each collaborator
    }

            // Assign status to the phase (default to 'pending' if not provided)
            StatusAssignment::create([
                'status_id' => $phaseData['phase_status_id'] ?? $pendingStatus->id,
                'statusable_id' => $phase->id,
                'statusable_type' => Phase::class,
            ]);

            // Loop through the activities for each phase and create them
            foreach ($phaseData['activities'] as $activityData) {
                $activity = $phase->activities()->create([
                    'activity_name' => $activityData['activity_name'],
                    'activity_budget' => $activityData['activity_budget']
                ]);

                // Assign status to the activity (default to 'pending' if not provided)
                StatusAssignment::create([
                    'status_id' => $activityData['activity_status_id'] ?? $pendingStatus->id,
                    'statusable_id' => $activity->id,
                    'statusable_type' => Activity::class,
                ]);
            }
        }

        return response()->json([
            'message' => 'Proposal created successfully with phases and activities',
            'data' => new ProposalResource($proposal->load(['phases.activities.statusAssignments']))
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $proposal = Proposal::with([
            'statusAssignments.status',
            'phases.statusAssignments.status',
            'phases.activities.statusAssignments.status'
        ])->find($id);
    
        if ($proposal) {
            return response()->json([
                'message' => 'Proposal found',
                'data' => new ProposalResource($proposal)
            ], 200);
        } else {
            return response()->json(['message' => 'Proposal not found'], 404);
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
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $proposal = Proposal::find($id);
        if ($proposal) {
            $proposal->delete();
            return response()->json([
                'message' => 'Proposal deleted successfully',
                'data' => new ProposalResource($proposal)
            ], 200);
        } else {
            return response()->json([
                'message' => 'Proposal not found'
            ], 404);
        }
    }
}
