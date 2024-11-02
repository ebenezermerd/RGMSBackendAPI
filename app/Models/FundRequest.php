<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_status', 'request_reason', 'request_amount', 'request_needed_date',
        'request_proof', 'user_id', 'activity_id', 'phase_id', 'proposal_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function statuses()
    {
        return $this->morphMany(StatusAssignment::class, 'statusable');
    }

    public function latestStatusAssignment()
{
    return $this->hasOne(StatusAssignment::class, 'statusable_id')
                ->where('statusable_type', self::class)
                ->latest();
                
}
}
