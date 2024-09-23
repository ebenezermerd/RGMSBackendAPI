<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = ['activity_name', 'activity_budget'];

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_activity');
    }
    public function statusAssignments()
{
    return $this->morphMany(StatusAssignment::class, 'statusable');
}

}
