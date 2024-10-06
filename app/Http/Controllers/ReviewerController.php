<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Review;
use App\Models\UserProposalAssignment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class ReviewerController extends Controller
{

    protected $statusAssignmentController;

    public function __construct(StatusAssignmentController $statusAssignmentController)
    {
        $this->statusAssignmentController = $statusAssignmentController;
    }

    // Get all reviewers for a specific COE class
    public function index()
    {
        $reviewers = User::whereHas('role', function ($query) {
            $query->where('role_name', 'reviewer');
        })->get();

        return response()->json($reviewers);
    }

    // Add a new reviewer
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->input('user_id'));
        $reviewerRole = Role::where('role_name', 'reviewer')->first();

        if ($user->role->role_name !== $reviewerRole) {
            $user->role_id = 3;
        }

        return response()->json(['message' => 'Reviewer added successfully.']);
    }

    // Remove a reviewer
    public function destroy($reviewerId)
    {
        $user = User::findOrFail($reviewerId);
        $reviewerRole = Role::where('role_name', 'reviewer')->first();

        if ($user->role->role_name === $reviewerRole) {
            $user->role_id = 2;
        }

        return response()->json(['message' => 'Reviewer removed successfully.']);
    }

    public function getAssignedProposals()
    {
        // Assuming the logged-in user is a reviewer
        $reviewer = auth()->user();

        if (!$reviewer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Fetch proposals assigned to the reviewer
        $assignedProposals = $reviewer->proposalsAssigned()->get();

        return response()->json($assignedProposals);
    }

    // Submit a review for a proposal
    public function submitReview(Request $request, $coeClassName, $proposalId)
    {
        $request->validate([
            'reviews' => 'required|array',
            'totalScore' => 'required|numeric',
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $proposal = Proposal::where('COE', $coeClassName)->findOrFail($proposalId);
        if (!$proposal) {
            return response()->json(['message' => 'Proposal not found'], 404);
        }

        // Check if the reviewer has already reviewed this proposal
        $existingReview = Review::where('proposal_id', $proposal->id)
                                ->where('reviewer_id', $request->reviewer_id)
                                ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Reviewer has already reviewed this proposal'], 403);
        }

        $reviewDataArray = [];
        foreach ($request->reviews as $section => $reviewData) {
            $review = Review::create([
                'proposal_id' => $proposal->id,
                'reviewer_id' => $request->reviewer_id,
                'section' => $section,
                'score' => $reviewData['score'],
                'comment' => $reviewData['comment'],
                'total_score' => $request->totalScore,
            ]);
            $reviewDataArray[] = $review;
        }

        // Update the proposal status to 'reviewed'
       
      $this->statusAssignmentController->updateProposalStatus($proposal, 'reviewed');

        return response()->json([
            'message' => 'Proposal reviewed and submitted successfully.',
            'reviewed_data' => $reviewDataArray
        ]);
    }
    

    // Get all reviews for a proposal
    public function getReviews($coeClassName, $proposalId)
    {
        $proposal = Proposal::where('COE', $coeClassName)->findOrFail($proposalId);
        $reviews = $proposal->reviews;
        return response()->json($reviews);
    }
}
