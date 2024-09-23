<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusAssignment;
use App\Models\Proposal;
use App\Models\Phase;
use App\Models\Activity;

class StatusAssignmentController extends Controller
{
    public function assignStatus(Request $request)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'statusable_type' => 'required|string',
            'statusable_id' => 'required|integer'
        ]);

        $statusAssignment = StatusAssignment::create([
            'status_id' => $request->status_id,
            'statusable_id' => $request->statusable_id,
            'statusable_type' => $request->statusable_type,
        ]);

        return response()->json($statusAssignment, 201);
    }

    public function getStatusesForEntity($type, $id)
    {
        $statuses = StatusAssignment::where('statusable_type', $type)
            ->where('statusable_id', $id)
            ->with('status')
            ->get();

        return response()->json($statuses);
    }
    public function getProposalStatus($user_id, $proposal_id)
{
    // Validate the proposal belongs to the user
    $proposal = Proposal::where('user_id', $user_id)->where('id', $proposal_id)->first();

    if (!$proposal) {
        return response()->json(['message' => 'Proposal not found for this user'], 404);
    }

    // Get status for the proposal
    $statusAssignments = StatusAssignment::where('statusable_type', 'App\\Models\\Proposal')
        ->where('statusable_id', $proposal->id)
        ->with('status')
        ->get();

    return response()->json(['statuses' => $statusAssignments->pluck('status.name')]);
}
public function getPhaseStatus($user_id, $proposal_id, $phase_id)
{
    // Validate the proposal and phase belong to the user
    $proposal = Proposal::where('user_id', $user_id)->where('id', $proposal_id)->first();
    $phase = Phase::where('id', $phase_id)->where('proposal_id', $proposal_id)->first();

    if (!$proposal || !$phase) {
        return response()->json(['message' => 'Phase not found for this user and proposal'], 404);
    }

    // Get status for the phase
    $statusAssignments = StatusAssignment::where('statusable_type', 'App\\Models\\Phase')
        ->where('statusable_id', $phase->id)
        ->with('status')
        ->get();

    return response()->json(['statuses' => $statusAssignments->pluck('status.name')]);
}
public function getActivityStatus($user_id, $proposal_id, $phase_id, $activity_id)
{
    // Validate the proposal, phase, and activity belong to the user
    $proposal = Proposal::where('user_id', $user_id)->where('id', $proposal_id)->first();
    $phase = Phase::where('id', $phase_id)->where('proposal_id', $proposal_id)->first();
    $activity = Activity::where('id', $activity_id)->where('phase_id', $phase_id)->first();

    if (!$proposal || !$phase || !$activity) {
        return response()->json(['message' => 'Activity not found for this user, proposal, and phase'], 404);
    }

    // Get status for the activity
    $statusAssignments = StatusAssignment::where('statusable_type', 'App\\Models\\Activity')
        ->where('statusable_id', $activity->id)
        ->with('status')
        ->get();

    return response()->json(['statuses' => $statusAssignments->pluck('status.name')]);
}



}
