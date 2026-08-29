<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BuyerInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $buyer,
        public string $temporaryPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Adili Real Estate',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.buyer-invitation',
            with: [
                'name' => $this->buyer->name,
                'email' => $this->buyer->email,
                'password' => $this->temporaryPassword,
                'loginUrl' => url('/forgot-password'),
            ]
        );
    }
}