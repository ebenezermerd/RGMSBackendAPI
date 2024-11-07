<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'whyApplyTitle', 'whyApplyContent', 'bulletPoints', 'buttonText', 'isActive', 'startDate', 'endDate', 'proposalType', 'isResubmissionAllowed', 'coverImage'
    ];

    protected $casts = [
        'bulletPoints' => 'array',
        'startDate' => 'date',
        'endDate' => 'date',
    ];
}
