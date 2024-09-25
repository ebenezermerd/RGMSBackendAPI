<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "first_name"=> $this->first_name,
            "last_name"=> $this->last_name,
            "username"=> $this->username,
            "email"=> $this->email,
            "organization"=> $this->organization,
            "role_id"=> $this->role_id,
            "phone_number"=> $this->phone_number,
            'coeClasses' => CoeClassResource::collection($this->whenLoaded('coeClass')),
            "created_at"=> $this->created_at->format('Y-m-d H:i:s'),
            "updated_at"=> $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
