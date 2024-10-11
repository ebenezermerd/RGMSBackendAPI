<?php

namespace App\Http\Controllers;

use App\Models\FundRequest;
use Illuminate\Http\Request;

class FundRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fundRequests = FundRequest::all();
        return response()->json($fundRequests);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return a view for creating a new fund request
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_status' => 'required|string',
            'request_reason' => 'required|string',
            'request_amount' => 'required|numeric',
            'request_proof' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'activity_id' => 'required|exists:activities,id',
            'phase_id' => 'required|exists:phases,id',
            'proposal_id' => 'required|exists:proposals,id',
        ]);

        $fundRequest = FundRequest::create($request->all());
        return response()->json($fundRequest, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fundRequest = FundRequest::findOrFail($id);
        return response()->json($fundRequest);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Return a view for editing the fund request
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'request_status' => 'required|string',
            'request_reason' => 'required|string',
            'request_amount' => 'required|numeric',
            'request_proof' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'activity_id' => 'required|exists:activities,id',
            'phase_id' => 'required|exists:phases,id',
            'proposal_id' => 'required|exists:proposals,id',
        ]);

        $fundRequest = FundRequest::findOrFail($id);
        $fundRequest->update($request->all());
        return response()->json($fundRequest);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fundRequest = FundRequest::findOrFail($id);
        $fundRequest->delete();
        return response()->json(null, 204);
    }

    /**
     * Display a listing of the resource for a specific user.
     */
    public function userFundRequests(string $user_id)
    {
        $fundRequests = FundRequest::where('user_id', $user_id)->get();
        return response()->json($fundRequests);
    }
}