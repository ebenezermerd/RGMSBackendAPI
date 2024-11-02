<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'report_type', 'report_id', 'report_date', 'report_title', 
        'report_content', 'report_file', 'user_id', 'phase_id', 'proposal_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
