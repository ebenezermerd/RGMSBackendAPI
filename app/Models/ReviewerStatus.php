<?php
// backend-laravel-server/app/Models/ReviewerStatus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_id',
        'first_name',
        'last_name',
        'expertise',
        'status',
    ];

    protected $casts = [
        'expertise' => 'array',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}