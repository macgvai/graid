<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
    ];

    /** Автор комментария */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Пост */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
