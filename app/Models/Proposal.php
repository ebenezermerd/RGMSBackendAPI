<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;
    protected $fillable = [
        'COE', 'proposal_title', 'proposal_abstract', 'proposal_introduction', 
        'proposal_literature', 'proposal_methodology', 'proposal_results', 
        'proposal_reference', 
         'proposal_submitted_date', 'proposal_end_date', 
        'proposal_budget', 'user_id', 'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phases()
    {
        return $this->hasMany(Phase::class);
    }

    public function collaborators()
    {
        return $this->hasMany(Collaborator::class);
    }

    public function statusAssignments()
    {
        return $this->morphMany(StatusAssignment::class, 'statusable');
    }
    

    public function research()
    {
        return $this->hasOne(Research::class);
    }

    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'user_proposal_assignments', 'proposal_id', 'reviewer_id')
                    ->withTimestamps(); // Using the custom pivot table
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // app/Models/Proposal.php
public function assignedReviewers()
{
    return $this->hasMany(UserProposalAssignment::class, 'proposal_id');
}

public function latestStatusAssignment()
{
    return $this->hasOne(StatusAssignment::class, 'statusable_id')
                ->where('statusable_type', self::class)
                ->latestOfMany();
}

}
