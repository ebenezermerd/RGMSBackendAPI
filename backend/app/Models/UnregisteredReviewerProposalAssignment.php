<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnregisteredReviewerProposalAssignment extends Model
{
    use HasFactory;
    protected $fillable = ['unregistered_reviewer_id', 'proposal_id', 'start_time', 'end_time', 'request_status', 'comment'];

    public function unregisteredReviewer()
    {
        return $this->belongsTo(UnregisteredReviewer::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}