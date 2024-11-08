<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusAssignmentResource;
use App\Models\User;
use App\Models\UserProposalAssignment;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Reviewer;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Models\UnregisteredReviewer;
use App\Models\UnregisteredReviewerProposalAssignment;
use Illuminate\Support\Facades\Mail;


class CoeProposalController extends Controller
{
    // Get all proposals for a specific COE class
    public function index($coeClassName)
    {
        $proposals = Proposal::where('COE', $coeClassName)
            ->with(['latestStatusAssignment','phases.latestStatusAssignment', 'phases.activities.latestStatusAssignment', 'phases', 'phases.activities', 'collaborators', 'reviews'])
            ->get();

        // Transform the user data using UserResource
        $proposals->each(function ($proposal) {
            $proposal->user = new UserResource($proposal->user);
            $proposal->latest_status = $proposal->latestStatusAssignment 
                ? new StatusAssignmentResource($proposal->latestStatusAssignment) 
                : null;
        });

      

        return response()->json($proposals);
    }


    // Get a single proposal by ID
    public function show($coeClassName, $proposalId)
    {
        $proposal = Proposal::where('COE', $coeClassName)
            ->with(['latestStatusAssignment', 'phases', 'phases.activities', 'collaborators'])
            ->findOrFail($proposalId);

            $proposal->latest_status = $proposal->latestStatusAssignment 
            ? new StatusAssignmentResource($proposal->latestStatusAssignment) 
            : null;

        return response()->json($proposal);
    }

    //Assign a reviewer to a proposal
    // public function assignReviewer(Request $request, $coeClassName, $proposalId)
    // {
    //     try {
    //         $request->validate([
    //             'reviewer_id' => 'required|exists:users,id',
    //         ]);

    //         // Find the proposal by COE and ID
    //         $proposal = Proposal::where('COE', $coeClassName)->findOrFail($proposalId);

    //         // Find the user by ID and check if they are a reviewer
    //         $reviewer = User::findOrFail($request->input('reviewer_id'));
    //         if ($reviewer->role->role_name !== 'reviewer') {
    //             return response()->json(['error' => 'User does not have the reviewer role.'], 403);
    //         }

    //         // Attach the reviewer to the proposal using the custom pivot table
    //         $proposal->reviewers()->attach($reviewer->id);

    //         return response()->json(['message' => 'Reviewer assigned successfully.']);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }


    public function assignReviewer(Request $request, $proposalId)
    {
        $request->validate([
            'reviewer_email' => 'required|email',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);
    
        $reviewer = User::where('email', $request->reviewer_email)->first();
        $proposal = Proposal::findOrFail($proposalId);
    
        if ($reviewer) {
            // Registered reviewer
            $assignment = UserProposalAssignment::create([
                'reviewer_id' => $reviewer->id,
                'proposal_id' => $proposal->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'request_status' => 'pending',
            ]);
    
            // Send email to registered reviewer
            Mail::to($reviewer->email)->send(new ReviewRequestMail($assignment));
        } else {
            // Unregistered reviewer
            $unregisteredReviewer = UnregisteredReviewer::firstOrCreate(['email' => $request->reviewer_email]);
    
            $assignment = UnregisteredReviewerProposalAssignment::create([
                'unregistered_reviewer_id' => $unregisteredReviewer->id,
                'proposal_id' => $proposal->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'request_status' => 'pending',
            ]);
    
            // Send email to unregistered reviewer
            Mail::to($request->reviewer_email)->send(new ReviewRequestMail($assignment));
        }
    
        return response()->json(['message' => 'Review request sent successfully']);
    }

    public function getAssignedReviewers($coeClassName, $proposalId)
    {
        $proposal = Proposal::where('COE', $coeClassName)->find($proposalId);
        if (!$proposal) {
            return response()->json(['message' => 'Proposal not found'], 404);
        }

        // $reviewers = Review::where('proposal_id', $proposalId)
        //                    ->with('reviewer')
        //                    ->get()
        //                    ->pluck('reviewer')
        //                    ->unique('id')
        //                    ->values();

        $reviewers = $proposal->reviewers()->get();

        return response()->json($reviewers);
    }

        public function getReviewedProposals($coeClassName)
    {
        $proposals = Proposal::with(['latestStatusAssignment', 'reviews', 'user', 'reviewers', 'phases', 'phases.activities', 'assignedReviewers'])
            ->where('COE', $coeClassName)
            ->get();
        // Filter only reviewed proposals (those with reviews, scores, and comments)
        $reviewedProposals = $proposals->filter(function ($proposal) {
            return $proposal->reviews->isNotEmpty(); // Check if proposal has reviewers assigned
        });

        // Add reviewers from reviews to each proposal
        $reviewedProposals->each(function ($proposal) {
            $reviewerIds = $proposal->reviews->pluck('reviewer_id')->unique();
            $reviewers = User::whereIn('id', $reviewerIds)->get();
            $proposal->reviewers_from_reviews = $reviewers;

            $proposal->reviews->each(function ($review) use ($reviewers) {
                $reviewer = User::find($review->reviewer_id);
                if ($reviewer) {
                    $review->reviewer_name = $reviewer->first_name . ' ' . $reviewer->last_name;
                }
            });
            
            $proposal->latest_status = $proposal->latestStatusAssignment 
            ? new StatusAssignmentResource($proposal->latestStatusAssignment) 
            : null;
        });

    

        return response()->json($reviewedProposals, 200);
    }




    // remove reivewer from the proposal
    public function removeReviewer($coeClassName, $proposalId, $reviewerId)
    {
        $proposal = Proposal::where('COE', $coeClassName)->findOrFail($proposalId);
        $reviewer = User::findOrFail($reviewerId);

        $proposal->reviewers()->detach($reviewer->id);

        return response()->json(['message' => 'Reviewer removed successfully.']);
    }


    // Download the proposal
    public function downloadProposal($coeClassName, $proposalId)
    {
        $proposal = Proposal::where('COE', $coeClassName)->findOrFail($proposalId);

        if (Storage::exists($proposal->file_path)) {
            // Use the actual path inside the storage/app folder
            return response()->download(storage_path("app/{$proposal->file_path}"));
        }


        return response()->json(['message' => 'File not found.'], 404);
    }


}
