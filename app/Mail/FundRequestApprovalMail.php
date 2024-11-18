<?php
// backend-laravel-server/app/Mail/FundRequestApprovalMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\FundRequest;

class FundRequestApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fundRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(FundRequest $fundRequest)
    {
        $this->fundRequest = $fundRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Fund Request Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.fund_request_approval',
            with: [
                'fundRequest' => $this->fundRequest,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}