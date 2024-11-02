<?php

namespace App\Http\Controllers;

use App\Http\Resources\FundRequestResource;
use App\Models\FundRequest;
use App\Models\Status;
use App\Models\Proposal;
use App\Models\StatusAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\FundRequestApproved;

class FundRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role_name; // Assuming you have a role field in your User model

        if ($role === 'admin' || $role === 'directorate') {
            $fundRequests = FundRequest::with('user')->get();
            
            if ($fundRequests->isEmpty()) {
                return response()->json(['message' => 'No fund requests found'], 404);
            }

            $fundRequestsData = $fundRequests->map(function ($fundRequest) {
                return [
                    'fund_request' => new FundRequestResource($fundRequest),
                    'user' => [
                        'first_name' => $fundRequest->user->first_name,
                        'last_name' => $fundRequest->user->last_name,
                        'email' => $fundRequest->user->email,
                        'profile_image' => $fundRequest->user->profile_image,
                    ]
                ];
            });

            return response()->json($fundRequestsData);
        } elseif ($role === 'coe') {
            $fundRequests = FundRequest::whereHas('proposal', function ($query) use ($user) {
                $coeClassName = strtolower(str_replace(' ', '-', $user->coeClass->name));
                $query->where('coe', $coeClassName);
            })->get();

            if ($fundRequests->isEmpty()) {
                return response()->json(['message' => 'No fund requests found'], 404);
            }

            $fundRequestsData = $fundRequests->map(function ($fundRequest) {
                $proposal = Proposal::find($fundRequest->proposal_id);
                $phase = $proposal->phases()->find($fundRequest->phase_id);
                $activity = $phase->activities()->find($fundRequest->activity_id);

                return [
                    'fund_request' => new FundRequestResource($fundRequest),
                    'coeClass' => $proposal->COE,
                    'type' => 'proposal',
                    'user' => [
                        'first_name' => $fundRequest->user->first_name,
                        'last_name' => $fundRequest->user->last_name,
                        'email' => $fundRequest->user->email,
                        'profile_image' => $fundRequest->user->profile_image,
                    ],
                    'phase_name' => $phase->phase_name,
                    'activity_name' => $activity->activity_name,
                ];
            });

            return response()->json($fundRequestsData);
        }
         else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
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
    public function store(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $type = $request->input('type');

        if ($type === 'proposal') {
            $request->validate([
                'request_reason' => 'nullable|string',
                'request_amount' => 'required|numeric',
                'request_needed_date' => 'required|date',
                'activity_id' => 'required|exists:activities,id',
                'phase_id' => 'required|exists:phases,id',
                'proposal_id' => 'required|exists:proposals,id',
            ]);
        } else {
            $request->validate([
                'request_reason' => 'required|string',
                'request_amount' => 'required|numeric',
                'request_needed_date' => 'required|date',
                'proposal_id' => 'required|exists:proposals,id',
                'activity_id' => 'nullable|exists:activities,id',
                'phase_id' => 'nullable|exists:phases,id',
            ]);
        }

        $pendingStatus = Status::firstOrCreate(['name' => 'pending']);

        // Create a new fund request instance
        $fundRequest = FundRequest::create([
            'request_reason' => $request->input('request_reason'),
            'request_amount' => $request->input('request_amount'),
            'request_needed_date' => $request->input('request_needed_date'),
            'user_id' => $userId, // Set the user_id field
            'activity_id' => $request->input('activity_id'),
            'phase_id' => $request->input('phase_id'),
            'proposal_id' => $request->input('proposal_id'),
        ]);

        StatusAssignment::create([
            'status_id' => $request->input('requested_status_id') ?? $pendingStatus->id,
            'statusable_id' => $fundRequest->id,
            'statusable_type' => FundRequest::class,
        ]);

        return response()->json([
            'message' => 'Fund request created successfully',
            'fund_request' => new FundRequestResource($fundRequest)
        ], 201);
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
            'request_reason' => 'nullable|string',
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
        return response()->json([
            'fundRequests' => FundRequestResource::collection($fundRequests)
        ], 200);
    }

    public function approve(Request $request, $id)
    {
        $fundRequest = FundRequest::findOrFail($id);
        $fundRequest->update(['request_status' => 'approved']);

        // Trigger the event
        event(new FundRequestApproved($fundRequest));

        return response()->json([
                'fundRequest' => new FundRequestResource($fundRequest),
        ], 200);
    }
}