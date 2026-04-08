<?php

namespace App\Services\Posts;

use App\Enums\PostType;
use App\Jobs\SendNewPostNotificationsJob;
use App\Models\ContentType;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PostPublishingService
{
    public function __construct(
        private readonly HashtagNormalizer $hashtagNormalizer,
        private readonly LinkPreviewService $linkPreviewService,
        private readonly RemoteImageStorageService $remoteImageStorageService,
        private readonly Repository $config,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function publish(User $author, PostType $postType, array $data, ?UploadedFile $uploadedImage = null): Post
    {
        /** @var Post $post */
        $post = DB::transaction(function () use ($author, $postType, $data, $uploadedImage): Post {
            $post = Post::query()->create($this->buildPayload($author, $postType, $data, $uploadedImage));

            $tagIds = collect($this->hashtagNormalizer->normalize((string) $data['tags']))
                ->map(static fn (string $tag): int => Hashtag::query()->firstOrCreate(['name' => $tag])->id)
                ->all();

            $post->hashtags()->sync($tagIds);

            SendNewPostNotificationsJob::dispatch($post->id)
                ->afterCommit()
                ->delay(now()->addSeconds((int) $this->config->get('notifications.mail_delay_seconds', 0)));

            return $post;
        });

        $post->load(['contentType', 'hashtags']);
        $author->loadCount(['followers', 'posts']);
        $post->setRelation('author', $author);

        return $post;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildPayload(User $author, PostType $postType, array $data, ?UploadedFile $uploadedImage): array
    {
        $payload = [
            'user_id' => $author->id,
            'content_type_id' => $this->resolveContentType($postType)->id,
            'title' => (string) $data['title'],
        ];

        return match ($postType) {
            PostType::Photo => $payload + [
                'image' => $uploadedImage !== null
                    ? $uploadedImage->store('posts/images', 'public')
                    : $this->remoteImageStorageService->downloadAndStore(
                        (string) $data['image_url'],
                        'posts/images',
                        'image_url',
                        'Не удалось загрузить изображение по указанной ссылке.',
                    ),
            ],
            PostType::Video => $payload + [
                'video' => (string) $data['video'],
            ],
            PostType::Text => $payload + [
                'text_content' => (string) $data['text_content'],
            ],
            PostType::Quote => $payload + [
                'text_content' => (string) $data['text_content'],
                'quote_author' => (string) $data['quote_author'],
            ],
            PostType::Link => $payload + [
                'link' => (string) $data['link'],
                'link_preview' => $this->linkPreviewService->fetchAndStore((string) $data['link']),
            ],
        };
    }

    private function resolveContentType(PostType $postType): ContentType
    {
        /** @var ContentType $contentType */
        $contentType = ContentType::query()->findOrFail($postType->value);

        return $contentType;
    }
}
