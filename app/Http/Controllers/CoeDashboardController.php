<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
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

        // Fetch statistics
        $proposalsReceived = Proposal::where('COE', $coeClassId)->count();
        $reviewersRegistered = User::where('role_id', 3)->count();

        // Return the dashboard data
        return response()->json([
            'proposalsReceived' => $proposalsReceived,
            'reviewersRegistered' => $reviewersRegistered,
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
