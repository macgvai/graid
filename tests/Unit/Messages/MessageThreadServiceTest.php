<?php

namespace Tests\Unit\Messages;

use App\Models\Message;
use App\Models\User;
use App\Services\Messages\MessageThreadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageThreadServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testItBuildsContactsAndConversationForSelectedUser(): void
    {
        $currentUser = User::factory()->create();
        $otherContact = User::factory()->create();
        $selectedContact = User::factory()->create();

        Message::factory()->create([
            'sender_id' => $otherContact->id,
            'receiver_id' => $currentUser->id,
            'content' => 'First dialog message',
        ]);
        Message::factory()->create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $selectedContact->id,
            'content' => 'Selected dialog first',
        ]);
        Message::factory()->create([
            'sender_id' => $selectedContact->id,
            'receiver_id' => $currentUser->id,
            'content' => 'Selected dialog second',
        ]);

        /** @var MessageThreadService $service */
        $service = $this->app->make(MessageThreadService::class);
        $thread = $service->build($currentUser, $selectedContact->id);

        self::assertCount(2, $thread['contacts']);
        self::assertSame($selectedContact->id, $thread['selected_user']?->id);
        self::assertSame(
            ['Selected dialog first', 'Selected dialog second'],
            $thread['conversation']->pluck('content')->all(),
        );
    }

    public function testItPrependsExplicitlySelectedUserWithoutConversation(): void
    {
        $currentUser = User::factory()->create();
        $existingContact = User::factory()->create();
        $selectedContact = User::factory()->create();

        Message::factory()->create([
            'sender_id' => $existingContact->id,
            'receiver_id' => $currentUser->id,
            'content' => 'Existing conversation',
        ]);

        /** @var MessageThreadService $service */
        $service = $this->app->make(MessageThreadService::class);
        $thread = $service->build($currentUser, $selectedContact->id);

        self::assertSame($selectedContact->id, $thread['contacts']->first()['user']->id);
        self::assertTrue($thread['conversation']->isEmpty());
    }
}
