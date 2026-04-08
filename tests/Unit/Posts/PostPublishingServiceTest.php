<?php

namespace Tests\Unit\Posts;

use App\Enums\PostType;
use App\Jobs\SendNewPostNotificationsJob;
use App\Models\User;
use App\Services\Posts\LinkPreviewService;
use App\Services\Posts\PostPublishingService;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PostPublishingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testItPublishesTextPostsWithNormalizedHashtags(): void
    {
        Queue::fake();
        $this->seed(ContentTypeSeeder::class);

        $author = User::factory()->create();

        /** @var PostPublishingService $service */
        $service = $this->app->make(PostPublishingService::class);
        $post = $service->publish($author, PostType::Text, [
            'title' => 'Service text post',
            'tags' => '#Travel sea sea',
            'text_content' => 'Service text content',
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'user_id' => $author->id,
            'content_type_id' => PostType::Text->value,
            'title' => 'Service text post',
            'text_content' => 'Service text content',
        ]);
        self::assertSame(['travel', 'sea'], $post->hashtags->pluck('name')->all());
        Queue::assertPushed(SendNewPostNotificationsJob::class);
    }

    public function testItUsesLinkPreviewServiceForLinkPosts(): void
    {
        Queue::fake();
        $this->seed(ContentTypeSeeder::class);

        $author = User::factory()->create();
        $link = 'https://example.com/article';
        $previewPath = 'posts/previews/generated-preview.jpg';

        $linkPreviewService = $this->mock(LinkPreviewService::class);
        $linkPreviewService
            ->shouldReceive('fetchAndStore')
            ->once()
            ->with($link)
            ->andReturn($previewPath);

        /** @var PostPublishingService $service */
        $service = $this->app->make(PostPublishingService::class);
        $post = $service->publish($author, PostType::Link, [
            'title' => 'Service link post',
            'tags' => 'link useful',
            'link' => $link,
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'user_id' => $author->id,
            'content_type_id' => PostType::Link->value,
            'title' => 'Service link post',
            'link' => $link,
            'link_preview' => $previewPath,
        ]);
        Queue::assertPushed(SendNewPostNotificationsJob::class);
    }
}
