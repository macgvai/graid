<?php

namespace App\Services\Subscriptions;

use App\Jobs\SendNewSubscriberNotificationJob;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubscribeToUserService
{
    public function __construct(
        private readonly Repository $config,
    ) {
    }

    public function subscribe(User $subscriber, User $target): Subscription
    {
        if ($subscriber->is($target)) {
            throw ValidationException::withMessages([
                'target_id' => 'Нельзя подписаться на самого себя.',
            ]);
        }

        /** @var Subscription $subscription */
        $subscription = DB::transaction(function () use ($subscriber, $target): Subscription {
            $subscription = Subscription::query()->firstOrCreate([
                'author_id' => $subscriber->id,
                'target_id' => $target->id,
            ]);

            if ($subscription->wasRecentlyCreated) {
                SendNewSubscriberNotificationJob::dispatch($subscription->id)
                    ->afterCommit()
                    ->delay(now()->addSeconds((int) $this->config->get('notifications.mail_delay_seconds', 0)));
            }

            return $subscription;
        });

        return $subscription;
    }

    public function unsubscribe(User $subscriber, User $target): void
    {
        $deleted = Subscription::query()
            ->where('author_id', $subscriber->id)
            ->where('target_id', $target->id)
            ->delete();

        if ($deleted === 0) {
            throw ValidationException::withMessages([
                'target_id' => 'Подписка для удаления не найдена.',
            ]);
        }
    }
}
