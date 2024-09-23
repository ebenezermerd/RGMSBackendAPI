<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PhaseResource;
use App\Models\Proposal;
use Illuminate\Support\Facades\Validator;
use App\Models\Phase;
use App\Models\StatusAssignment;
use App\Models\Status;

class PhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($proposalId)
{
    $phases = Phase::where('proposal_id', $proposalId)
                   ->with('statusAssignments.status') // Eager load status information
                   ->get();
    
    return response()->json([
        'message' => 'Phases retrieved successfully',
        'data' => PhaseResource::collection($phases)
    ]);
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
    public function store(Request $request, $proposalId)
{
    // Validate incoming request data
    $validator = Validator::make($request->all(), [
        'phase_name' => 'required|string|max:255',
        'phase_startdate' => 'required|date',
        'phase_enddate' => 'required|date',
        'phase_objective' => 'required|string',
        'phase_status_id' => 'nullable|exists:statuses,id', // Optional status ID
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $proposal = Proposal::findOrFail($proposalId);

    // Retrieve or create 'pending' status ID
    $pendingStatus = Status::firstOrCreate(['name' => 'pending']);

    // Create the phase
    $phase = Phase::create([
        'phase_name' => $request->phase_name,
        'phase_startdate' => $request->phase_startdate,
        'phase_enddate' => $request->phase_enddate,
        'phase_objective' => $request->phase_objective,
        'proposal_id' => $proposal->id,
    ]);

    // Assign status to the phase (default to 'pending' if not provided)
    StatusAssignment::create([
        'status_id' => $request->phase_status_id ?? $pendingStatus->id,
        'statusable_id' => $phase->id,
        'statusable_type' => Phase::class,
    ]);

    return response()->json(['message' => 'Phase created successfully', 'data' => $phase], 201);
}


    /**
     * Display the specified resource.
     */
    public function show($phaseId)
    {
        $phase = Phase::with('statusAssignments.status')->findOrFail($phaseId);
    
        return response()->json([
            'message' => 'Phase retrieved successfully',
            'data' => new PhaseResource($phase)
        ]);
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
    public function destroy(string $id)
    {
        //
    }
}
