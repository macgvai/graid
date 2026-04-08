<?php

namespace Tests\Feature;

use App\Enums\PostType;
use App\Jobs\SendNewPostNotificationsJob;
use App\Models\User;
use Database\Seeders\ContentTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanPublishPhotoPostWithUploadedFileAndTags(): void
    {
        Queue::fake();
        Storage::fake('public');
        $this->seed(ContentTypeSeeder::class);

        $author = User::factory()->create();

        $response = $this
            ->actingAs($author)
            ->post(route('posts.store', ['type' => PostType::Photo->value]), [
                'post_type' => PostType::Photo->value,
                'title' => 'Новый кадр',
                'tags' => 'travel summer sea',
                'image_file' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'user_id' => $author->id,
            'content_type_id' => PostType::Photo->value,
            'title' => 'Новый кадр',
        ]);
        $this->assertDatabaseHas('hashtags', ['name' => 'travel']);
        $this->assertDatabaseHas('hashtags', ['name' => 'summer']);
        $this->assertDatabaseHas('hashtags', ['name' => 'sea']);
        $this->assertNotEmpty(Storage::disk('public')->allFiles('posts/images'));
        Queue::assertPushed(SendNewPostNotificationsJob::class);
    }

    public function testVideoPostRequiresYouTubeUrl(): void
    {
        $this->seed(ContentTypeSeeder::class);
        $author = User::factory()->create();

        $response = $this
            ->actingAs($author)
            ->from(route('adding-post'))
            ->post(route('posts.store', ['type' => PostType::Video->value]), [
                'post_type' => PostType::Video->value,
                'title' => 'Видео',
                'tags' => 'video blog',
                'video' => 'https://vimeo.com/123',
            ]);

        $response
            ->assertRedirect(route('adding-post'))
            ->assertSessionHasErrors('video');

        $this->assertDatabaseCount('posts', 0);
    }

    public function testLinkPostDownloadsPreviewImage(): void
    {
        Queue::fake();
        Storage::fake('public');
        Http::fake([
            'https://api.thumbnail.ws/*' => Http::response(base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+XKZ8AAAAASUVORK5CYII='
            ), 200, ['Content-Type' => 'image/png']),
        ]);

        config()->set('services.thumbnail_ws.key', 'test-key');
        $this->seed(ContentTypeSeeder::class);

        $author = User::factory()->create();

        $response = $this
            ->actingAs($author)
            ->post(route('posts.store', ['type' => PostType::Link->value]), [
                'post_type' => PostType::Link->value,
                'title' => 'Полезная ссылка',
                'tags' => 'link useful',
                'link' => 'https://example.com/article',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'user_id' => $author->id,
            'content_type_id' => PostType::Link->value,
            'title' => 'Полезная ссылка',
            'link' => 'https://example.com/article',
        ]);
        $this->assertNotEmpty(Storage::disk('public')->allFiles('posts/previews'));
        Queue::assertPushed(SendNewPostNotificationsJob::class);
    }
}
