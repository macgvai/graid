<?php

namespace App\Jobs;

use App\Mail\NewSubscriberMail;
use App\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNewSubscriberNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $subscriptionId,
    ) {
    }

    public function handle(): void
    {
        /** @var Subscription $subscription */
        $subscription = Subscription::query()
            ->with(['author', 'target'])
            ->findOrFail($this->subscriptionId);

        Mail::to($subscription->target->email)->send(app(NewSubscriberMail::class, [
            'recipient' => $subscription->target,
            'subscriber' => $subscription->author,
            'subscriberProfileUrl' => route('users.show', $subscription->author),
        ]));
    }
}
