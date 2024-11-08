<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function proposals()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function research()
    {
        return $this->belongsTo(Research::class);
    }
    public function phases()
    {
        return $this->belongsTo(Phase::class);
    }
    public function activities()
    {
        return $this->belongsTo(Activity::class);
    }

    public function statusAssignments()
    {
        return $this->hasMany(StatusAssignment::class);
    }

}
