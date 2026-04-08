<?php

namespace App\Services\Posts;

use App\Jobs\SendNewPostNotificationsJob;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\DB;

class RepostPostService
{
    public function __construct(
        private readonly Repository $config,
    ) {
    }

    public function repost(User $author, Post $source): Post
    {
        $source->loadMissing(['hashtags', 'author']);

        /** @var Post $repost */
        $repost = DB::transaction(function () use ($author, $source): Post {
            $repost = Post::query()->create([
                'user_id' => $author->id,
                'original_post_id' => $source->original_post_id ?? $source->id,
                'original_author_id' => $source->original_author_id ?? $source->user_id,
                'content_type_id' => $source->content_type_id,
                'title' => $source->title,
                'text_content' => $source->text_content,
                'quote_author' => $source->quote_author,
                'image' => $source->image,
                'video' => $source->video,
                'link' => $source->link,
                'link_preview' => $source->link_preview,
                'is_repost' => true,
                'views' => 0,
            ]);

            $repost->hashtags()->sync($source->hashtags->pluck('id')->all());

            SendNewPostNotificationsJob::dispatch($repost->id)
                ->afterCommit()
                ->delay(now()->addSeconds((int) $this->config->get('notifications.mail_delay_seconds', 0)));

            return $repost;
        });

        return $repost->load(['author', 'contentType', 'hashtags', 'originalAuthor', 'originalPost.author']);
    }
}
