<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PhaseResource extends JsonResource
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
            'phase_name' => $this->phase_name,
            'phase_startdate' => $this->phase_startdate,
            'phase_enddate' => $this->phase_enddate,
            'phase_objective' => $this->phase_objective,
            'proposal_id' => $this->proposal_id,
            'status_assignments' => StatusAssignmentResource::collection($this->whenLoaded('statusAssignments')),
            'latest_status' => $this->latestStatusAssignment ? new StatusAssignmentResource($this->latestStatusAssignment) : null,
            'activities' => ActivityResource::collection($this->activities),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
