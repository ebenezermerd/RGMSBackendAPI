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
            "id" => $this->id,
            "sender_id" => $this->sender_id,
            "receiver_id" => $this->receiver_id,
            "message_subject" => $this->message_subject,
            "message_content" => $this->message_content,
            "attachments" => $this->attachments,  // Corrected typo
            "is_read" => $this->is_read,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
