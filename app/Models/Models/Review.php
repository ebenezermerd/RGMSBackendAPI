<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id', 'reviewer_id', 'section', 'score', 'comment',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
