<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;
    protected $fillable = [
        'collaborator_name', 'collaborator_gender', 
        'collaborator_organization', 'collaborator_phone_number', 
        'collaborator_email', 'verified', 'proposal_id'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
