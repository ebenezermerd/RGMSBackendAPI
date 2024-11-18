<?php

namespace App\Http\Controllers;

use App\Http\Resources\FundRequestResource;
use App\Models\FundRequest;
use App\Models\Status;
use App\Models\Activity;
use App\Models\Proposal;
use App\Models\StatusAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\FundRequestApproved;
use App\Http\Resources\StatusAssignmentResource;
use App\Mail\FundRequestApprovalMail;
use App\Models\Message;
use App\Models\Phase;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

class FundRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->role_name;
    
        $query = FundRequest::query();
    
        if ($role === 'admin' || $role === 'directorate' || $role === 'coe') {
            $query->with('user');
        } elseif ($role === 'coe') {
            $query->whereHas('proposal', function ($query) use ($user) {
                $coeClassName = strtolower(str_replace(' ', '-', $user->coeClass->name));
                $query->where('coe', $coeClassName);
            });
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $filter = $request->input('filter');
        $sort = $request->input('sort', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
    
        if ($search) {
            $query->where('request_reason', 'like', "%{$search}%");
        }
    
        if ($filter && $filter !== 'all') {
            $query->whereHas('latestStatusAssignment.status', function ($query) use ($filter) {
                $query->where('name', $filter);
            });
        }
    
        switch ($sort) {
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('request_amount', 'desc');
                break;
            case 'amount_asc':
                $query->orderBy('request_amount', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    
        $fundRequests = $query->paginate($perPage, ['*'], 'page', $page);
    
        if ($fundRequests->isEmpty()) {
            return response()->json(['message' => 'No fund requests found'], 404);
        }
    
        $fundRequestsData = collect($fundRequests->items())->map(function ($fundRequest) {
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
                'proposal_title' => $proposal->proposal_title,
                'phase_name' => $phase->phase_name,
                'activity_name' => $activity->activity_name,
            ];
        });
    
        return response()->json([
            'data' => $fundRequestsData,
            'totalPages' => $fundRequests->lastPage(),
            'currentPage' => $fundRequests->currentPage(),
            'totalItems' => $fundRequests->total(),
            'itemsPerPage' => $fundRequests->perPage(),
        ]);
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

        $request->validate([
            'request_reason' => 'nullable|string',
            'request_amount' => 'required|numeric',
            'request_needed_date' => 'required|date',
            'activity_id' => 'required|exists:activities,id',
        ]);

        $activity = Activity::findOrFail($request->input('activity_id'));
        $phase = Phase::findOrFail($activity->phase_id);
        $proposal = Proposal::findOrFail($phase->proposal_id);

        // Calculate the total budget of the phase
        $totalPhaseBudget = $phase->phase_budget;

        // Calculate the total requested amount for the activity
        $totalRequestedAmountForActivity = FundRequest::where('activity_id', $activity->id)
            ->get()
            ->filter(function ($fundRequest) {
            $latestStatusAssignment = $fundRequest->latestStatusAssignment;
            return $latestStatusAssignment && (new StatusAssignmentResource($latestStatusAssignment))->status_name === 'approved';
            })
            ->sum('request_amount');

        // Calculate the remaining budget for the activity
        $remainingBudgetForActivity = $activity->remaining_budget - $totalRequestedAmountForActivity;

        // Calculate the allowed percentage for the phase
        $allowedPercentage = 0.30; // 30% as an example, you can make this configurable

        // Calculate the maximum allowed request amount for the phase
        $maxAllowedRequestAmountForPhase = $totalPhaseBudget * $allowedPercentage;

        // Check if the requested amount exceeds the remaining budget for the activity
        if ($request->input('request_amount') > $remainingBudgetForActivity) {
            return response()->json([
                'message' => "The requested amount exceeds the remaining budget for the activity. Only {$remainingBudgetForActivity} birr left to be requested."
            ], 400);
        }

        // Check if the requested amount exceeds the allowed percentage for the phase
        if ($request->input('request_amount') > $maxAllowedRequestAmountForPhase) {
            return response()->json([
                'message' => "The requested amount exceeds the allowed percentage for the phase. Only {$maxAllowedRequestAmountForPhase} birr can be requested."
            ], 400);
        }

        $pendingStatus = Status::firstOrCreate(['name' => 'pending']);

        // Create a new fund request instance
        $fundRequest = FundRequest::create([
            'request_reason' => $request->input('request_reason'),
            'request_amount' => $request->input('request_amount'),
            'request_needed_date' => $request->input('request_needed_date'),
            'user_id' => $userId,
            'activity_id' => $request->input('activity_id'),
            'phase_id' => $activity->phase_id,
            'proposal_id' => $proposal->id,
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
     * Approve the status of a given fund request for a given proposal activity.
     */
    public function approveStatus(Request $request, $userId, $fundRequestId)
    {
        $user = User::findOrFail($userId);
        $coeRole = Role::where('role_name', 'coe')->firstOrFail();

        if ($user->role_id !== $coeRole->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $fundRequest = FundRequest::findOrFail($fundRequestId);

        // Get the status id for approved
        $approvedStatus = Status::where('name', 'approved')->firstOrFail();

        // Update the status assignment
        $statusAssignment = StatusAssignment::where('statusable_id', $fundRequestId)
            ->where('statusable_type', FundRequest::class)
            ->latest()
            ->first();

        if ($statusAssignment) {
            $statusAssignment->update(['status_id' => $approvedStatus->id]);
        } else {
            StatusAssignment::create([
                'status_id' => $approvedStatus->id,
                'statusable_id' => $fundRequestId,
                'statusable_type' => FundRequest::class,
            ]);
        }

        // Create a new transaction using the TransactionController
        $transactionController = new TransactionController();
        $transactionRequest = new Request([
            'transaction_date' => now(),
            'transaction_amount' => $fundRequest->request_amount,
            'transaction_type' => 'N/A',
            'transaction_description' => 'Transaction for the approved fund request dated ' . now()->toDateString() . '. The request amounting to ' . $fundRequest->request_amount . ' birr has been successfully approved. This transaction signifies the allocation of the requested funds to the specified activity under the proposal. The approval status confirms that the necessary checks and validations have been completed, and the funds are now ready for disbursement. Please ensure to utilize the allocated funds as per the guidelines and timelines specified in the proposal. The approval and allocation process is now complete, and the transaction is recorded for future reference.',
            'fund_request_id' => $fundRequest->id,
            'user_id' => $fundRequest->user_id,
        ]);
        $transactionController->store($transactionRequest);

        // Send email to the user
        Mail::to($fundRequest->user->email)->send(new FundRequestApprovalMail($fundRequest));
        
        // Send system message to the user
        $messageContent = "Your fund request for the activity '{$fundRequest->activity->activity_name}' has been approved. The amount of {$fundRequest->request_amount} birr has been approved on " . now()->toDateString() . ". Please ensure to utilize the allocated funds as per the guidelines and timelines specified in the proposal. The approval and allocation process is now complete, and the transaction is recorded for future reference.";
        $sender_type = 'coe_class';

        Message::create([
            'sender_id' => $user->id,
            'sender_type' => $sender_type,
            'receiver_id' => $fundRequest->user_id,
            'message_subject' => 'Fund Request Approved',
            'message_content' => $messageContent,
            'attachments' => null,
        ]);

        // Update the corresponding budget amounts
        $activity = $fundRequest->activity;
        $activity->remaining_budget -= $fundRequest->request_amount;
        $activity->save();
      
        $phase = $fundRequest->phase;
        $phase->remaining_budget -= $fundRequest->request_amount;
        $phase->save();

        $proposal = $fundRequest->proposal;
        $proposal->remaining_budget -= $fundRequest->request_amount;
        $proposal->save();
        
        // Optionally, you can trigger an event or perform additional actions here

        return response()->json([
            'success' => 'Fund request status updated successfully',
            'fund_request' => new FundRequestResource($fundRequest),
        ], 200);
    }
    /**
     * Reject the status of a given fund request for a given proposal activity.
     */
    public function rejectStatus( $userId, $fundRequestId)
    {
        $user = User::findOrFail($userId);
        $coeRole = Role::where('role_name', 'coe')->firstOrFail();

        if ($user->role_id !== $coeRole->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $fundRequest = FundRequest::findOrFail($fundRequestId);

        // Get the status id for rejected
        $rejectedStatus = Status::where('name', 'rejected')->firstOrFail();

        // Update the status assignment
        $statusAssignment = StatusAssignment::where('statusable_id', $fundRequestId)
            ->where('statusable_type', FundRequest::class)
            ->latest()
            ->first();

        if ($statusAssignment) {
            $statusAssignment->update(['status_id' => $rejectedStatus->id]);
        } else {
            StatusAssignment::create([
                'status_id' => $rejectedStatus->id,
                'statusable_id' => $fundRequestId,
                'statusable_type' => FundRequest::class,
            ]);
        }

        // Optionally, you can trigger an event or perform additional actions here

        return response()->json([
            'success' => 'Fund request status updated to rejected successfully',
            'fund_request' => new FundRequestResource($fundRequest),
        ], 200);
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
    public function update(Request $request, $userId,  $requestId)
    {
        $request->validate([
            'request_reason' => 'nullable|string',
            'request_amount' => 'required|numeric',
            'request_needed_date' => 'required|date',
            'activity_id' => 'required|exists:activities,id',
        ]);

      // Find the fund request or throw a 404 error
      $fundRequest = FundRequest::findOrFail($requestId);

      // Fetch the latest status assignment for this fund request
      $latestStatusAssignment = StatusAssignment::where('statusable_id', $fundRequest->id)
          ->where('statusable_type', FundRequest::class)
          ->latest()
          ->first();
  
      // Transform the status assignment into a resource array
      $latest_status_data = $latestStatusAssignment
          ? (new StatusAssignmentResource($latestStatusAssignment))->toArray(request())
          : null;
  
      // Check if the latest status is not "rejected"
    if ($latest_status_data && !in_array($latest_status_data['status_name'], ["rejected", "pending"])) {
        return response()->json([
          'message' => 'Only rejected or pending fund requests can be updated and resent',
          'status' => $latest_status_data['status_name'] ?? 'N/A',
          'fund_request' => new FundRequestResource($fundRequest),
        ], 400);
      }

        $activity = Activity::findOrFail($request->input('activity_id'));
        $phase = Phase::findOrFail($activity->phase_id);
        $proposal = Proposal::findOrFail($phase->proposal_id);

        // Calculate the total budget of the phase
        $totalPhaseBudget = $phase->phase_budget;

        // Calculate the total requested amount for the activity
        $totalRequestedAmountForActivity = FundRequest::where('activity_id', $activity->id)
            ->get()
            ->filter(function ($fundRequest) {
                $latestStatusAssignment = $fundRequest->latestStatusAssignment;
                return $latestStatusAssignment && (new StatusAssignmentResource($latestStatusAssignment))->status_name === 'approved';
            })
            ->sum('request_amount');

        // Calculate the remaining budget for the activity
        $remainingBudgetForActivity = $activity->remaining_budget - $totalRequestedAmountForActivity;

        // Calculate the allowed percentage for the phase
        $allowedPercentage = 0.30; // 30% as an example, you can make this configurable

        // Calculate the maximum allowed request amount for the phase
        $maxAllowedRequestAmountForPhase = $totalPhaseBudget * $allowedPercentage;

        // Check if the requested amount exceeds the remaining budget for the activity
        if ($request->input('request_amount') > $remainingBudgetForActivity) {
            return response()->json([
                'message' => "The requested amount exceeds the remaining budget for the activity. Only {$remainingBudgetForActivity} birr left to be requested."
            ], 400);
        }

        // Check if the requested amount exceeds the allowed percentage for the phase
        if ($request->input('request_amount') > $maxAllowedRequestAmountForPhase) {
            return response()->json([
                'message' => "The requested amount exceeds the allowed percentage for the phase. Only {$maxAllowedRequestAmountForPhase} birr can be requested."
            ], 400);
        }

        $pendingStatus = Status::firstOrCreate(['name' => 'pending']);

        // Update the fund request
        $fundRequest->update([
            'request_reason' => $request->input('request_reason'),
            'request_amount' => $request->input('request_amount'),
            'request_needed_date' => $request->input('request_needed_date'),
            'activity_id' => $request->input('activity_id'),
            'phase_id' => $activity->phase_id,
            'proposal_id' => $proposal->id,
        ]);

        // Update the status to pending
        StatusAssignment::create([
            'status_id' => $pendingStatus->id,
            'statusable_id' => $fundRequest->id,
            'statusable_type' => FundRequest::class,
        ]);

        return response()->json([
            'message' => 'Fund request updated and resent successfully',
            'fund_request' => new FundRequestResource($fundRequest)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($userId, $requestId)
    {
        $user = User::findOrFail($userId);
        $fundRequest = FundRequest::findOrFail($requestId);

        // Fetch the latest status assignment for this fund request
      $latestStatusAssignment = StatusAssignment::where('statusable_id', $fundRequest->id)
      ->where('statusable_type', FundRequest::class)
      ->latest()
      ->first();

        if ($user->id !== $fundRequest->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Transform the status assignment into a resource array
      $latest_status_data = $latestStatusAssignment
      ? (new StatusAssignmentResource($latestStatusAssignment))->toArray(request())
      : null;

  // Check if the latest status is not "pending"
  if ($latest_status_data && $latest_status_data['status_name'] === "pending") {
            $fundRequest->delete();
            return response()->json(['message' => 'Fund request deleted successfully'], 200);
        } else {
            return response()->json([
                'message' => 'Fund request cannot be deleted as it is not in pending state',
                'status' => $latest_status_data['status_name'] ?? 'N/A',
            
            ], 403);
        }
    }
    /**
     * Retrieve the fund requests for a given user.
     */
    public function getUserFundRequests($userId)
    {
        $user = User::findOrFail($userId);
    
        $fundRequests = FundRequest::where('user_id', $user->id)->get();
    
        if ($fundRequests->isEmpty()) {
            return response()->json(['message' => 'No fund requests found for this user'], 404);
        }
    
        return response()->json([
            'fundRequests' => FundRequestResource::collection($fundRequests)
        ], 200);
    }
}
/**
 * Retrieve the fund requests for a given user.
 */