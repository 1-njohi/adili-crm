<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Plot;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $agent,
        public Plot $plot,
        public User $buyer
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Lead Has Purchased a Plot!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agent-notification',
            with: [
                'agentName' => $this->agent->name,
                'plotNumber' => $this->plot->plot_number,
                'projectName' => $this->plot->project->name,
                'buyerName' => $this->buyer->name,
                'buyerEmail' => $this->buyer->email,
            ]
        );
    }
}