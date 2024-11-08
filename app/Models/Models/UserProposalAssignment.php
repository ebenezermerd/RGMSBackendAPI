<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProposalAssignment extends Model
{
    use HasFactory;
    protected $fillable = ['reviewer_id', 'proposal_id', 'start_time', 'end_time', 'request_status', 'comment'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    
}

