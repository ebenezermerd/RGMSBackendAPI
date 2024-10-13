<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundRequestResource extends JsonResource
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
            'request_status' => $this->request_status,
            'request_reason' => $this->request_reason,
            'request_amount' => $this->request_amount,
            'request_needed_date' => $this->request_needed_date ? $this->request_needed_date->format('Y-m-d H:i:s') : null,
            'request_proof' => $this->request_proof,
            'user_id' => $this->user_id,
            'activity_id' => $this->activity_id,
            'phase_id' => $this->phase_id,
            'proposal_id' => $this->proposal_id,
            'latest_status' => new StatusAssignmentResource($this->lateststatusassignment),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
