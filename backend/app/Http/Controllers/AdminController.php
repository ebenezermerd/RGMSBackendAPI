<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CoeClass;
use App\Models\Phase;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\StatusAssignmentResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\UserCoeAssignment;
use App\Models\Role;

class AdminController extends Controller
{

    protected $statusAssignmentController;

    public function __construct(StatusAssignmentController $statusAssignmentController)
    {
        $this->statusAssignmentController = $statusAssignmentController;
    }
    
    public function index(Request $request)
    {
        // Check if the authenticated user is an admin
        if (!in_array($request->user()->role->role_name, ['admin', 'directorate'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $users = User::with(['role', 'coeClass', 'proposals'])->get();

        $users->each(function ($user) {
            $user->proposals->each(function ($proposal) {
                $proposal->latest_status = new StatusAssignmentResource($proposal->latestStatusAssignment);
            });
        });

        return $users;
    }

    public function show(User $user)
    {
        if (!in_array(Auth::user()->role->role_name, ['admin', 'directorate'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $user->load('role');
    }

    public function update(Request $request, User $user)
    {
        // Ensure only admins can change roles
        
        $validator = Validator::make($request->all(), [
            'role' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'User role updated successfully']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function toggleResearchCallState(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'state' => 'required|boolean',
        ]);

        $admin = User::find($request->admin_id);
        $admin->research_call_state = $request->state;
        $admin->save();

        return response()->json(['message' => 'Research call state updated successfully']);
    }

    public function getCallToggleState(Request $request)
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('role_name', 'admin');
        })->first();

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        return response()->json(['state' => $admin->research_call_state]);
    }

    //get all proposals
    public function getAllProposals()
    {
        $proposals = Proposal::with(['user', 'phases', 'phases.activities','collaborators', 'reviews'])->get();

        $proposals->each(function ($proposal) {
            $proposal->latest_status = new StatusAssignmentResource($proposal->latestStatusAssignment);
        });

        return response()->json($proposals);
    }

    public function updateStatus(Request $request,  $proposal_id)
    {
        //dd($coeName, $proposal_id);
        $validated = $request->validate([
            'status' => 'required|string', 
            'type' => 'required|string|in:proposal,phase,activity',
            'reason' => 'nullable|string'
        ]);

        $status = $validated['status'];
        $type = $validated['type'];

        try {
            switch ($type) {
                case 'proposal':
                    $model = Proposal::where('id', $proposal_id)->firstOrFail();
                    break;
                case 'phase':
                    $model = Phase::where('proposal_id', $proposal_id)->firstOrFail();
                    break;
                case 'activity':
                    $model = Activity::whereHas('phase', function ($query) use ($proposal_id) {
                        $query->where('proposal_id', $proposal_id);
                    })->firstOrFail();
                    break;
                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }

            $this->statusAssignmentController->updateStatus($model, $status, $request->reason);
            return response()->json(['message' => 'Status updated successfully'], 200);
        } catch (NotFoundHttpException $e) {
            return response()->json(['error' => 'Resource not found'], 404);
        }
    }

      // lets add here a function that will assign a user to role of coe
      // lets add here a function that will assign a user to role of coe
      public function changeUserRole(Request $request)
      {
          $request->validate([
              'user_id' => 'required|exists:users,id',
              'role' => 'required|in:coe,admin,reviewer,researcher,auditor,directorate',
          ]);
  
          $user = User::find($request->user_id);
  
          if (!$user) {
              return response()->json(['message' => 'User not found'], 404);
          }
  
          // Check if the role is being changed from 'coe' to another role
          if ($user->role->role_name === 'coe' && $request->role !== 'coe') {
              // Remove COE class relationship
              UserCoeAssignment::where('user_id', $user->id)->delete();
          }
  
          // Get the role id from the roles table
          $role = Role::where('role_name', $request->role)->first();
  
          if (!$role) {
              return response()->json(['message' => 'Role not found'], 404);
          }
  
          $user->role_id = $role->id;
          $user->save();
  
          return response()->json([
              'message' => 'User role updated successfully',
              'data' => $user
          ]);
      }
  
  
      // Assign a COE class to a user
      public function assignUserToCoe(Request $request)
      {
          $request->validate([
              'user_id' => 'required|exists:users,id',
              'coe_class_id' => 'required|exists:coe_classes,id',
          ]);
  
          // Check if the user already has the coe role
          $user = User::find($request->user_id);
  
          if ($user->role->role_name !== 'coe') {
              return response()->json(['error' => 'User must have COE role to be assigned'], 400);
          }
  
          // Create or update the assignment
          UserCoeAssignment::updateOrCreate(
              ['user_id' => $request->user_id],
              ['coe_class_id' => $request->coe_class_id]
          );
  
          return response()->json(['message' => 'User successfully assigned to COE class']);
      }
  
  
  
      // Show assignments for a specific COE class
      public function showAssignments($coeClassId)
      {
          $coeClass = CoeClass::with('userCoeAssignments.user')->find($coeClassId);
  
          if (!$coeClass) {
              return response()->json(['message' => 'COE class not found'], 404);
          }
  
          return response()->json($coeClass->userCoeAssignments);
      }
  
  
      
  
}

