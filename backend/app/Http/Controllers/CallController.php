<?php
// app/Http/Controllers/CallController.php
namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

        // Validate the request data
        $validateUser = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'sometimes|nullable|string',
            'whyApplyTitle' => 'sometimes|nullable|string|max:255',
            'whyApplyContent' => 'sometimes|nullable|string',
            'bulletPoints' => 'sometimes|nullable|array',
            'bulletPoints.*' => 'sometimes|nullable|string',
            'buttonText' => 'sometimes|required|string|max:50',
            'isActive' => 'sometimes|required|boolean',
            'startDate' => 'sometimes|required|date',
            'endDate' => 'sometimes|required|date|after_or_equal:startDate',
            'proposalType' => 'sometimes|required|string|max:50',
            'isResubmissionAllowed' => 'sometimes|required|boolean',
            'coverImage' => 'sometimes|nullable', // Allow both file and URL
        ]);

        if ($validateUser->fails()) {
            return response()->json(['errors' => $validateUser->errors()], 422);
        }

        $validatedData = $validateUser->validated();

        // Convert bulletPoints array to JSON string if it exists
        if (isset($validatedData['bulletPoints'])) {
            $validatedData['bulletPoints'] = json_encode($validatedData['bulletPoints']);
        }

        // Handle the cover image upload
        if ($request->hasFile('coverImage')) {
            // Get the uploaded file
            $coverImage = $request->file('coverImage');

            // Generate a unique file name with the current timestamp and original extension
            $fileName = 'call_cover_' . time() . '.' . $coverImage->getClientOriginalExtension();

            // Store the file in the 'public/call_cover_images' directory
            $coverImagePath = $coverImage->storeAs('call_cover_images', $fileName, 'public'); // Store in public disk

            // Save the path to the validated data
            $validatedData['coverImage'] = $coverImagePath; // Store relative path
        } elseif (isset($validatedData['coverImage']) && filter_var($validatedData['coverImage'], FILTER_VALIDATE_URL)) {
            // If coverImage is a valid URL, save it directly
            $validatedData['coverImage'] = $validatedData['coverImage'];
        }

        // Create a new Call instance with the validated data
        $call = Call::create($validatedData);
        return response()->json($call, 201);
    }
}