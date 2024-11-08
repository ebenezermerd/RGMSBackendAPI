<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['post_title', 'post_description', 'post_image', 'post_date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
