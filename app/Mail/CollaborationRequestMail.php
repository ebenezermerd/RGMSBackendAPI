<?php
// backend-laravel-server/app/Mail/CollaborationRequestMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Proposal;
use App\Models\Collaborator;
use App\Models\User;

class CollaborationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $proposal;
    public $collaborator;
    public $user;
    public $companyName = 'Addis Ababa Science and Technology University Research Grant Management System';

    public function __construct(Proposal $proposal, Collaborator $collaborator, User $user)
    {
        $this->proposal = $proposal;
        $this->collaborator = $collaborator;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Collaboration Request')
                    ->view('emails.collaboration_request')
                    ->with([
                        'proposalTitle' => $this->proposal->proposal_title,
                        'collaboratorName' => $this->collaborator->collaborator_name,
                        'requesterFullName' => $this->user->first_name . ' ' . $this->user->last_name,
                        'companyName' => $this->companyName,
                    ]);
    }
}