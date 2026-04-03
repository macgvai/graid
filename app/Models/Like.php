<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    /** @use HasFactory<\Database\Factories\LikeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    public $timestamps = true;

    /** Кто поставил лайк */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Какой пост лайкнули */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
