<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusAssignmentResource;
use App\Models\Status;
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

    public function getStatusAssignments($statusableType, $statusableId)
    {
        $statuses = StatusAssignment::where('statusable_type', $statusableType)
            ->where('statusable_id', $statusableId)
            ->with('status')
            ->get();

        return response()->json($statuses);
    }

    public function updateStatus($model, $newStatusName, $reason)
    {
        $status = Status::where('name', $newStatusName)->firstOrFail();
        $this->assignStatusToModel($model, $status, $reason);
    }

    public function getProposalStatus($coeName, $proposal_id)
    {

        //dd($proposal_id, $coeName);

        // Validate the proposal belongs to the user
        $proposal = Proposal::where('id', $proposal_id)->first();

        if (!$proposal) {
            return response()->json(['message' => 'Proposal not found for this user'], 404);
        }

        // Get status for the proposal
        $latestStatusAssignment = StatusAssignment::where('statusable_type', 'App\\Models\\Proposal')
            ->where('statusable_id', $proposal->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestStatusAssignment) {
            return response()->json(['message' => 'No status found for the proposal'], 404);
        }

        return new StatusAssignmentResource($latestStatusAssignment);
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
        $latestStatusAssignment = StatusAssignment::where('statusable_type', 'App\\Models\\Phase')
            ->where('statusable_id', $phase->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestStatusAssignment) {
            return response()->json(['message' => 'No status found for the phase'], 404);
        }

        return new StatusAssignmentResource($latestStatusAssignment);
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

        $latestStatusAssignment = StatusAssignment::where('statusable_type', 'App\\Models\\Activity')
            ->where('statusable_id', $activity->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestStatusAssignment) {
            return response()->json(['message' => 'No status found for the activity'], 404);
        }

        return new StatusAssignmentResource($latestStatusAssignment);
    }


    public function initializeProposalStatus(Proposal $proposal)
    {
        $status = Status::where('name', 'pending')->firstOrFail();
        $this->assignStatusToModel($proposal, $status);

        $proposal->load('phases.activities'); // Ensure relationships are loaded

        foreach ($proposal->phases as $phase) {
            $this->assignStatusToModel($phase, $status);
            foreach ($phase->activities as $activity) {
                $this->assignStatusToModel($activity, $status);
            }
        }
    }
    private function assignStatusToModel($model, Status $status, $reason = null)
    {
        StatusAssignment::create([
            'status_id' => $status->id,
            'statusable_id' => $model->id,
            'statusable_type' => get_class($model),
            'reason' => $reason,
        ]);
    }
    public function updateProposalStatus(Proposal $proposal, $newStatusName)
    {
        $status = Status::where('name', $newStatusName)->firstOrFail();
        $this->assignStatusToModel($proposal, $status);

    }





}
