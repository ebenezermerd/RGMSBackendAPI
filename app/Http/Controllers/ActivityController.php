<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phase;
use Illuminate\Support\Facades\Validator;
use App\Models\Activity;
use App\Models\StatusAssignment;
use App\Models\Status;
use App\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($phaseId)
{
    $activities = Activity::where('phase_id', $phaseId)
                          ->with('statusAssignments.status')
                          ->get();

    return response()->json([
        'message' => 'Activities retrieved successfully',
        'data' => ActivityResource::collection($activities)
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
    public function store(Request $request, $phaseId)
{
    // Validate incoming request data
    $validator = Validator::make($request->all(), [
        'activity_name' => 'required|string|max:255',
        'activity_budget' => 'required|numeric',
        'activity_status_id' => 'nullable|exists:statuses,id', // Optional status ID
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find the phase or fail
    $phase = Phase::findOrFail($phaseId);

    // Retrieve or create 'pending' status ID
    $pendingStatus = Status::firstOrCreate(['name' => 'pending']);

    // Create the activity
    $activity = Activity::create([
        'activity_name' => $request->activity_name,
        'activity_budget' => $request->activity_budget,
        'phase_id' => $phase->id,
    ]);

    // Assign status to the activity (default to 'pending' if not provided)
    StatusAssignment::create([
        'status_id' => $request->activity_status_id ?? $pendingStatus->id,
        'statusable_id' => $activity->id,
        'statusable_type' => Activity::class,
    ]);

    return response()->json(['message' => 'Activity created successfully', 'data' => $activity], 201);
}

    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $activity = Activity::with('statusAssignments.status')->findOrFail($id);
        return new ActivityResource($activity);
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
