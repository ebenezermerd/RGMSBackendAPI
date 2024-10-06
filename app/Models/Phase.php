<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;
    protected $fillable = ['phase_name', 'phase_startdate', 'phase_enddate', 'phase_objective', 'proposal_id'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
    public function statusAssignments()
    {
        return $this->morphMany(StatusAssignment::class, 'statusable');
    }
    public function latestStatusAssignment()
    {
        return $this->hasOne(StatusAssignment::class, 'statusable_id')
                    ->where('statusable_type', self::class)
                    ->latestOfMany();
    }
}

