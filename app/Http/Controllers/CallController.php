<?php
// app/Http/Controllers/CallController.php
namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;
use App\Models\User;

class CallController extends Controller
{

        // Method to get the latest call (public)
        public function index()
        {
            $latestCall = Call::latest()->first();
            if ($latestCall) {
                return response()->json(['data' => $latestCall], 200);
            } else {
                return response()->json(['message' => 'No calls found'], 404);
            }
        }

        
    public function store(Request $request, string $adminId)
    {
        // Check if the user is an admin
        $admin = User::find($adminId);
        if (!$admin || $admin->role->role_name !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Destructure formData from the request
        $formData = $request->input('formData');

        // Validate the formData
        $validatedData = $request->validate([
            'formData.title' => 'required|string|max:255',
            'formData.subtitle' => 'nullable|string',
            'formData.whyApplyTitle' => 'nullable|string|max:255',
            'formData.whyApplyContent' => 'nullable|string',
            'formData.bulletPoints' => 'nullable|array',
            'formData.bulletPoints.*' => 'nullable|string',
            'formData.buttonText' => 'required|string|max:50',
            'formData.isActive' => 'required|boolean',
            'formData.startDate' => 'required|date',
            'formData.endDate' => 'required|date|after_or_equal:formData.startDate',
            'formData.proposalType' => 'required|string|max:50',
            'formData.isResubmissionAllowed' => 'required|boolean',
        ]);

        // Create a new Call instance with the validated data
        $call = Call::create($validatedData['formData']);
        return response()->json($call, 201);
    }
}