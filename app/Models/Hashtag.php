<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    /** @use HasFactory<\Database\Factories\HashtagFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    /** Посты с этим хештегом */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'hashtag_post');
    }
}
