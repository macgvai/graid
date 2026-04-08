<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewSubscriberMail extends Mailable
{
    use Queueable;

    public function __construct(
        public readonly User $recipient,
        public readonly User $subscriber,
        public readonly string $subscriberProfileUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'У вас новый подписчик',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-subscriber',
        );
    }
}
