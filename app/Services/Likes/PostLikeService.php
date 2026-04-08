<?php

namespace App\Services\Likes;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;

class PostLikeService
{
    public function like(User $user, Post $post): Like
    {
        $like = Like::query()->firstOrCreate([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        return $like->load(['user', 'post']);
    }
}
