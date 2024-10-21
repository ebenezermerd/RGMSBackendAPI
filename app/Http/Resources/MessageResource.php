<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            "user_id"=> $this->user_id,
            "sender_name"=> $this->sender_name,
            "message_subject"=> $this->message_subject,
            "message_content"=> $this->message_content,
            "receiver_email"=> $this->receiver_email,
            "message_date"=> $this->message_date,
            "profile_image"=> $this->profile_image,
            "created_at"=> $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'=> $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
