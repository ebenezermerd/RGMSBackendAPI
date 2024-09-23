<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Collaborator;
use App\Http\Resources\CollaboratorResource;

class CollaboratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($proposalId)
    {
        $collaborators = Collaborator::where('proposal_id', $proposalId)
                                    ->get();
    
        return response()->json([
            'message' => 'Collaborators retrieved successfully',
            'data' => CollaboratorResource::collection($collaborators)
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
        $validatedData = $request->validate([
            'collaborator_name' => 'required|string',
            'collaborator_gender' => 'required|string',
            'collaborator_organization' => 'required|string',
            'collaborator_phone_number' => 'required|string',
            'collaborator_email' => 'required|email',
        ]);

        $proposal = Proposal::findOrFail($proposalId);

        $collaborator = $proposal->collaborators()->create($validatedData);

        return new CollaboratorResource($collaborator);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
