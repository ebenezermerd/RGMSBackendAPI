<?php
// backend-laravel-server/app/Http/Controllers/ComplaintController.php

namespace App\Http\Controllers;

use App\Http\Resources\StatusAssignmentResource;
use App\Models\Complaint;
use App\Models\Status;
use App\Models\StatusAssignment;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['latestStatusAssignment.status'])->get();
        $complaints = $complaints->map(function ($complaint) {
            $complaint->latest_status = new StatusAssignmentResource($complaint->latestStatusAssignment);
            unset($complaint->latestStatusAssignment);
            return $complaint;
        });
        return response()->json($complaints);
    }

    public function store(Request $request)
    {
        $request->validate([
            'coe' => 'required|string',
            'complaint' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments');
                $attachments[] = $path;
            }
        }

        $formattedCoe = str_replace(' ', '-', strtolower($request->coe));
        $complaint = Complaint::create([
            'coe' => $formattedCoe,
            'complaint' => $request->complaint,
            'attachments' => $attachments,
        ]);

        // Assign initial status to the complaint (default to 'Pending')
        $pendingStatus = Status::firstOrCreate(['name' => 'Pending']);
        StatusAssignment::create([
            'status_id' => $pendingStatus->id,
            'statusable_id' => $complaint->id,
            'statusable_type' => Complaint::class,
        ]);

        // Fetch the complaint with the latest status
        $complaint = Complaint::with(['latestStatusAssignment.status'])->find($complaint->id);
        $complaint->latest_status = new StatusAssignmentResource($complaint->latestStatusAssignment);
        unset($complaint->latestStatusAssignment);

        return response()->json($complaint, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $complaint = Complaint::findOrFail($id);

        $attachments = $complaint->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments');
                $attachments[] = $path;
            }
        }

        $complaint->update([
            'response' => $request->response,
            'attachments' => $attachments,
        ]);

        // Update status to 'Responded'
        $respondedStatus = Status::firstOrCreate(['name' => 'Responded']);
        StatusAssignment::create([
            'status_id' => $respondedStatus->id,
            'statusable_id' => $complaint->id,
            'statusable_type' => Complaint::class,
        ]);

        // Fetch the complaint with the latest status
        $complaint = Complaint::with(['latestStatusAssignment.status'])->find($complaint->id);
        $complaint->latest_status = new StatusAssignmentResource($complaint->latestStatusAssignment);
        unset($complaint->latestStatusAssignment);

        return response()->json($complaint, 200);
    }

    public function getComplaintsByCOE($coe)
    {
        $complaints = Complaint::with(['latestStatusAssignment.status'])->where('coe', $coe)->get();
        $complaints = $complaints->map(function ($complaint) {
            $complaint->latest_status = new StatusAssignmentResource($complaint->latestStatusAssignment);
            unset($complaint->latestStatusAssignment);
            return $complaint;
        });
        return response()->json($complaints);
    }
}