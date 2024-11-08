<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'currency', 'time_zone', 
        'want_reminder', 'want_news_and_updates', 'want_recommendations'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
