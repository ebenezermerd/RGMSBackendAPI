<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'activity_name' => $this->activity_name,
            'activity_budget' => $this->activity_budget,
            'remaining_budget' => $this->remaining_budget,
            'phase_id' => $this->phase_id,
            'status_assignments' => StatusAssignmentResource::collection($this->whenLoaded('statusAssignments')),
            'latest_status' => $this->latestStatusAssignment ? new StatusAssignmentResource($this->latestStatusAssignment) : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
