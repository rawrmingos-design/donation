<?php

namespace App\Mail;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMilestone extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $campaign;
    public $milestone;

    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign, int $milestone)
    {
        $this->campaign = $campaign;
        $this->milestone = $milestone;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->campaign->user->email,
            subject: '🎯 Milestone ' . $this->milestone . '% Tercapai - ' . $this->campaign->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign-milestone',
            with: [
                'campaign' => $this->campaign,
                'milestone' => $this->milestone,
            ]
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
