<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status_id' => $this->status_id,
            'status_name' => $this->status->name, // Pulls the status name from the `statuses` table
            'assigned_at' => $this->created_at->toDateTimeString(), // When the status was assigned
        ];
    }
}
