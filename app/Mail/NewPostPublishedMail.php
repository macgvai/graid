<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewPostPublishedMail extends Mailable
{
    use Queueable;

    public function __construct(
        public readonly User $recipient,
        public readonly Post $post,
        public readonly string $authorProfileUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Новая публикация от пользователя %s', $this->post->author->login),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-post-published',
        );
    }
}
