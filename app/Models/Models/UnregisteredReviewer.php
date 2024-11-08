<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnregisteredReviewer extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'full_name'];

    public function assignments()
    {
        return $this->hasMany(UnregisteredReviewerProposalAssignment::class);
    }
}
