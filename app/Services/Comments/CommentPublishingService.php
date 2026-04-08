<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentPublishingService
{
    public function publish(User $author, Post $post, string $content): Comment
    {
        $comment = Comment::query()->create([
            'user_id' => $author->id,
            'post_id' => $post->id,
            'content' => $content,
        ]);

        return $comment->load(['author', 'post']);
    }
}
