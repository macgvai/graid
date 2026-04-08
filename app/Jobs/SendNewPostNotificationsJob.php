<?php

namespace App\Jobs;

use App\Mail\NewPostPublishedMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNewPostNotificationsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $postId,
    ) {
    }

    public function handle(): void
    {
        /** @var Post $post */
        $post = Post::query()
            ->with(['author'])
            ->findOrFail($this->postId);

        $recipients = User::query()
            ->whereHas('subscriptions', static function ($query) use ($post): void {
                $query->where('target_id', $post->user_id);
            })
            ->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(app(NewPostPublishedMail::class, [
                'recipient' => $recipient,
                'post' => $post,
                'authorProfileUrl' => route('users.show', $post->author),
            ]));
        }
    }
}
