<?php

namespace Tests\Feature;

use App\Enums\PostType;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function testApiRegisterLoginAndLogoutEndpointsWork(): void
    {
        $registerResponse = $this->postJson(route('api.register'), [
            'email' => 'api@example.com',
            'login' => 'api-user',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $registerResponse->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'api@example.com',
            'login' => 'api-user',
        ]);

        $loginResponse = $this->postJson(route('api.login'), [
            'email' => 'api@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertOk();
        $loginResponse->assertJsonStructure(['token', 'user' => ['id', 'email', 'login']]);

        $user = User::query()->where('email', 'api@example.com')->firstOrFail();
        Sanctum::actingAs($user);

        $logoutResponse = $this->postJson(route('api.logout'));
        $logoutResponse->assertNoContent();
    }

    public function testApiPostUserAndSubscriptionEndpointsWork(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $user = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($user);

        $storeResponse = $this->postJson(route('api.posts.store', ['type' => PostType::Text->value]), [
            'post_type' => PostType::Text->value,
            'title' => 'API пост',
            'tags' => 'api test',
            'text_content' => 'Текст API поста',
        ]);

        $storeResponse->assertCreated();
        $postId = (int) $storeResponse->json('data.id');

        $this->getJson(route('api.posts.index'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $postId);

        $this->getJson(route('api.posts.show', $postId))
            ->assertOk()
            ->assertJsonPath('data.id', $postId);

        $this->postJson(route('api.comments.store', $postId), [
            'content' => 'API комментарий',
        ])->assertCreated();

        $this->postJson(route('api.likes.store', $postId))
            ->assertCreated();

        $this->postJson(route('api.posts.repost', $postId))
            ->assertCreated();

        $this->getJson(route('api.users.show', $user))
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);

        $this->postJson(route('api.subscriptions.store', $target))
            ->assertCreated();

        $this->deleteJson(route('api.subscriptions.destroy', $target))
            ->assertNoContent();
    }

    public function testApiFeedSearchPopularAndMessageEndpointsWork(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $viewer = User::factory()->create();
        $target = User::factory()->create();
        Sanctum::actingAs($viewer);

        $viewer->subscriptions()->create(['target_id' => $target->id]);

        $matchingPost = Post::factory()->create([
            'user_id' => $target->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'API лента',
            'text_content' => 'Искомый текст',
            'views' => 99,
        ]);
        $hashtag = Hashtag::query()->create(['name' => 'api']);
        $matchingPost->hashtags()->attach($hashtag);

        $this->getJson(route('api.feed.index'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $matchingPost->id);

        $this->getJson(route('api.search.index', ['query' => '#api']))
            ->assertOk()
            ->assertJsonPath('data.data.0.id', $matchingPost->id);

        $this->getJson(route('api.popular.index'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $matchingPost->id);

        $this->postJson(route('api.messages.store'), [
            'receiver_id' => $target->id,
            'content' => 'Сообщение через API',
        ])->assertCreated();

        $this->getJson(route('api.messages.index', ['user' => $target->id]))
            ->assertOk()
            ->assertJsonPath('conversation.0.content', 'Сообщение через API');
    }
}
