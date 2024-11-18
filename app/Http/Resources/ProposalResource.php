<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'COE' => $this->COE,
            'proposal_title' => $this->proposal_title,
            'proposal_abstract' => $this->proposal_abstract,
            'proposal_introduction' => $this->proposal_introduction,
            'proposal_literature' => $this->proposal_literature,
            'proposal_methodology' => $this->proposal_methodology,
            'proposal_results' => $this->proposal_results,
            'proposal_reference' => $this->proposal_reference,
            'proposal_start_date' => $this->proposal_start_date,
            'proposal_end_date' => $this->proposal_end_date,
            'proposal_budget' => $this->proposal_budget,
            'remaining_budget' => $this->remaining_budget,
            'latest_status' => $this->latestStatusAssignment ? new StatusAssignmentResource($this->latestStatusAssignment) : null,
            'status_assignments' => StatusAssignmentResource::collection($this->whenLoaded('statusAssignments')),
            'phases' => PhaseResource::collection($this->phases),
            'collaborators' => CollaboratorResource::collection($this->collaborators),
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
