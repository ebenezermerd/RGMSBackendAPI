<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollaboratorResource extends JsonResource
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
            'collaborator_name' => $this->collaborator_name,
            'collaborator_gender' => $this->collaborator_gender,
            'collaborator_organization' => $this->collaborator_organization,
            'collaborator_phone_number' => $this->collaborator_phone_number,
            'collaborator_email' => $this->collaborator_email,
            'verified' => $this->verified,
            'proposal_id' => $this->proposal_id, // Foreign key
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
