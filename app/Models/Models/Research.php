<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;
    protected $fillable = [
        'research_title', 'start_date', 'end_date', 
        'research_budget', 'research_status_id', 'proposal_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
