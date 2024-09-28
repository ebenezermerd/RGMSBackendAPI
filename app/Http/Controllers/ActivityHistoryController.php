<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityHistory;
use Illuminate\Http\Request;

class ActivityHistoryController extends Controller
{
    public function index()
    {
        $activities = ActivityHistory::orderBy('created_at', 'desc')->get();
        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $activity = ActivityHistory::create([
            'description' => $request->description,
            'user_id' => $request->user_id,
        ]);

        return response()->json($activity, 201);
    }
}