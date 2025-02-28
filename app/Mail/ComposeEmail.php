<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComposeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $content;
    public $leadIds;
    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $content
     * @param array $leadIds
     * @return void
     */
    public function __construct($subject, $content, $leadIds)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->leadIds = $leadIds;
    }
   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.compose') // Your Blade view for email body
                    ->subject($this->subject) // Set the email subject
                    ->with([
                        'content' => $this->content, // Pass content to the view
                    ]);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Compose Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
