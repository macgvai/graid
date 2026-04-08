<?php

namespace Tests\Feature;

use App\Jobs\SendNewSubscriberNotificationJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanSubscribeToAnotherUser(): void
    {
        Queue::fake();

        $subscriber = User::factory()->create();
        $target = User::factory()->create();

        $response = $this
            ->actingAs($subscriber)
            ->post(route('subscriptions.store', $target));

        $response->assertRedirect(route('users.show', $target));
        $this->assertDatabaseHas('subscriptions', [
            'author_id' => $subscriber->id,
            'target_id' => $target->id,
        ]);
        Queue::assertPushed(SendNewSubscriberNotificationJob::class);
    }

    public function testUserCanUnsubscribeFromUser(): void
    {
        $subscriber = User::factory()->create();
        $target = User::factory()->create();

        $subscriber->subscriptions()->create([
            'target_id' => $target->id,
        ]);

        $response = $this
            ->actingAs($subscriber)
            ->delete(route('subscriptions.destroy', $target));

        $response->assertRedirect(route('users.show', $target));
        $this->assertDatabaseMissing('subscriptions', [
            'author_id' => $subscriber->id,
            'target_id' => $target->id,
        ]);
    }
}
