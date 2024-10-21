<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['sender_name', 'message_subject', 'message_content', 'receiver_email', 'message_date', 'profile_image', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
