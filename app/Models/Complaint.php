<?php
// backend-laravel-server/app/Models/Complaint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'coe', 'complaint', 'response', 'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function statusAssignments()
    {
        return $this->morphMany(StatusAssignment::class, 'statusable');
    }

    public function latestStatusAssignment()
{
    return $this->hasOne(StatusAssignment::class, 'statusable_id')
                ->where('statusable_type', self::class)
                ->latest();
                
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}