<?php

namespace Tests\Feature;

use App\Enums\PostType;
use App\Jobs\SendNewPostNotificationsJob;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RepostAndMessageTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRepostExistingPostWithOriginalMetadata(): void
    {
        Queue::fake();
        $this->seed(ContentTypeSeeder::class);

        $originalAuthor = User::factory()->create();
        $reposter = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $originalAuthor->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Исходный пост',
            'text_content' => 'Контент поста',
        ]);
        $hashtag = Hashtag::query()->create(['name' => 'travel']);
        $post->hashtags()->attach($hashtag);

        $response = $this
            ->actingAs($reposter)
            ->post(route('posts.repost', $post));

        $response->assertRedirect(route('users.show', $reposter));
        $this->assertDatabaseHas('posts', [
            'user_id' => $reposter->id,
            'original_post_id' => $post->id,
            'original_author_id' => $originalAuthor->id,
            'is_repost' => true,
            'title' => 'Исходный пост',
        ]);
        $this->assertDatabaseHas('hashtag_post', [
            'hashtag_id' => $hashtag->id,
        ]);
        Queue::assertPushed(SendNewPostNotificationsJob::class);
    }

    public function testUserCanSendMessageToAnotherUser(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this
            ->actingAs($sender)
            ->post(route('messages.store'), [
                'receiver_id' => $receiver->id,
                'content' => 'Привет, это личное сообщение',
            ]);

        $response->assertRedirect(route('messages', ['user' => $receiver->id]));
        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Привет, это личное сообщение',
        ]);
    }
}
