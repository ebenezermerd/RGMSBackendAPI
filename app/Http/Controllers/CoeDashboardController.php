<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusAssignmentResource;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CoeDashboardController extends Controller
{
    /**
     * Display the CoE dashboard.
     *
     * @param  string  $coeClassId
     * @return \Illuminate\Http\Response
     */
    public function index($coeClassId)
    {
        // Ensure COE class ID is valid
        $validCoEClasses = [
            'artificial-intelligence-and-robotics',
            'biotechnology-and-bioprocess',
            'construction-quality-and-technology',
            'high-performance-computing-and-big-data-analytics',
            'mineral-exploration-extraction-and-processing',
            'nano-technology',
            'nuclear-reactor-technology',
            'sustainable-energy',
        ];

        if (!in_array($coeClassId, $validCoEClasses)) {
            return response()->json(['error' => 'Invalid COE class'], 400);
        }
        $role = Role::where('role_name', 'reviewer')->first();
        // Fetch statistics
        $proposalsReceived = Proposal::where('COE', $coeClassId)->count();
        $reviewersRegistered = User::where('role_id', $role->id)->count();
        $approvedProposals = Proposal::where('COE', $coeClassId)
            ->with('latestStatusAssignment')
            ->get()
            ->filter(function ($proposal) {
            $statusAssignment = new StatusAssignmentResource($proposal->latestStatusAssignment);
            return $statusAssignment->status_name === 'approved';
            })
            ->count();
        // Return the dashboard data
        return response()->json([
            'proposalsReceived' => $proposalsReceived,
            'reviewersRegistered' => $reviewersRegistered,
            'approvedProposals' => $approvedProposals,
        ]);
    }

    /**
     * Get additional statistics for the CoE.
     *
     * @param  string  $coeClassId
     * @return \Illuminate\Http\Response
     */
    public function stats($coeClassId)
    {
        // Ensure COE class ID is valid
        $validCoEClasses = [
            'artificial-intelligence-and-robotics',
            'biotechnology-and-bioprocess',
            'construction-quality-and-technology',
            'high-performance-computing-and-big-data-analytics',
            'mineral-exploration-extraction-and-processing',
            'nano-technology',
            'nuclear-reactor-technology',
            'sustainable-energy',
        ];

        if (!in_array($coeClassId, $validCoEClasses)) {
            return response()->json(['error' => 'Invalid COE class'], 400);
        }

        // Fetch detailed statistics
        $totalProposals = Proposal::where('COE', $coeClassId)->count();
        $pendingReviews = Proposal::where('COE', $coeClassId)
            ->where('status', 'pending-review')
            ->count();
        $completedReviews = Proposal::where('COE', $coeClassId)
            ->where('status', 'completed-review')
            ->count();

        // Return detailed statistics
        return response()->json([
            'totalProposals' => $totalProposals,
            'pendingReviews' => $pendingReviews,
            'completedReviews' => $completedReviews,
        ]);
    }
}
