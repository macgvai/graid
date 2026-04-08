<?php

namespace Tests\Feature;

use App\Enums\PostType;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentAndLikeTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanAddCommentAndIsRedirectedToPostAuthorProfile(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $author = User::factory()->create();
        $commentator = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Пост автора',
        ]);

        $response = $this
            ->actingAs($commentator)
            ->post(route('comments.store', $post), [
                'content' => 'Это валидный комментарий',
            ]);

        $response->assertRedirect(route('users.show', $author));
        $this->assertDatabaseHas('comments', [
            'user_id' => $commentator->id,
            'post_id' => $post->id,
            'content' => 'Это валидный комментарий',
        ]);
    }

    public function testUserCanLikePostAndIsRedirectedBack(): void
    {
        $this->seed(ContentTypeSeeder::class);

        $author = User::factory()->create();
        $liker = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Пост для лайка',
        ]);

        $response = $this
            ->actingAs($liker)
            ->from(route('main'))
            ->post(route('likes.store', $post));

        $response->assertRedirect(route('main'));
        $this->assertDatabaseHas('likes', [
            'user_id' => $liker->id,
            'post_id' => $post->id,
        ]);
    }
}
