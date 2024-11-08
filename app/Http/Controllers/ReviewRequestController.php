<?php

namespace App\Http\Controllers;

use App\Models\UnregisteredReviewerProposalAssignment;
use Illuminate\Http\Request;
use App\Models\UserProposalAssignment;

class ReviewRequestController extends Controller
{
    public function showResponsePage($id)
    {
        $assignment = UserProposalAssignment::find($id) ?? UnregisteredReviewerProposalAssignment::findOrFail($id);
        return view('review_response', compact('assignment'));
    }

    public function handleResponse(Request $request, $id)
    {
        $assignment = UserProposalAssignment::find($id) ?? UnregisteredReviewerProposalAssignment::findOrFail($id);

        $request->validate([
            'response' => 'required|in:accepted,rejected',
            'comment' => 'nullable|string',
        ]);

        $assignment->update([
            'request_status' => $request->response,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Response recorded successfully']);
    }
}
