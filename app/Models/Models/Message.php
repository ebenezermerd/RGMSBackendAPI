<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [ 'sender_id', 'sender_type', 'receiver_id', 'message_subject', 
    'message_content', 'attachments', 'user_id', 'is_read' ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
