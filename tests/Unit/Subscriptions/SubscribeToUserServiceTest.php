<?php

namespace Tests\Unit\Subscriptions;

use App\Jobs\SendNewSubscriberNotificationJob;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Subscriptions\SubscribeToUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubscribeToUserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesSubscriptionAndQueuesNotification(): void
    {
        Queue::fake();

        $subscriber = User::factory()->create();
        $target = User::factory()->create();

        /** @var SubscribeToUserService $service */
        $service = $this->app->make(SubscribeToUserService::class);
        $subscription = $service->subscribe($subscriber, $target);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'author_id' => $subscriber->id,
            'target_id' => $target->id,
        ]);
        Queue::assertPushed(SendNewSubscriberNotificationJob::class, 1);
    }

    public function testItDoesNotQueueNotificationForExistingSubscription(): void
    {
        Queue::fake();

        $subscriber = User::factory()->create();
        $target = User::factory()->create();
        Subscription::factory()->create([
            'author_id' => $subscriber->id,
            'target_id' => $target->id,
        ]);

        /** @var SubscribeToUserService $service */
        $service = $this->app->make(SubscribeToUserService::class);
        $service->subscribe($subscriber, $target);

        $this->assertDatabaseCount('subscriptions', 1);
        Queue::assertNothingPushed();
    }
}
