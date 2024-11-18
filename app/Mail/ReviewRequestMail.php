<?php
// backend-laravel-server/app/Mail/ReviewRequestMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;
    public $coe;
    public $isRegistered;

    /**
     * Create a new message instance.
     */
    public function __construct($assignment, $coe, $isRegistered = true)
    {
        $this->assignment = $assignment;
        $this->coe = $coe;
        $this->isRegistered = $isRegistered;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Review Request From ' . ucwords(str_replace('-', ' ', $this->coe)),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $responseUrl = $this->isRegistered
            ? url('/review-request/' . $this->assignment->id . '/response')
            : url('/review-request/' . $this->assignment->id . '/view');

        return new Content(
            view: 'emails.review_request',
            with: [
                'assignment' => $this->assignment,
                'coe' => $this->coe,
                'responseUrl' => $responseUrl,
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