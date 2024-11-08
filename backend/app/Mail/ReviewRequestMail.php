<?php

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
    public $isRegistered;

    /**
     * Create a new message instance.
     */
    public function __construct($assignment, $isRegistered = true)
    {
        $this->assignment = $assignment;
        $this->isRegistered = $isRegistered;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Review Request',
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
