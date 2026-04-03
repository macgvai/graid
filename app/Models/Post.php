<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_type_id',
        'title',
        'text_content',
        'quote_author',
        'image',
        'video',
        'link',
        'views',
    ];

    /** Автор */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Тип контента */
    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    /** Комментарии к посту */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Лайки */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /** Хештеги (many-to-many) */
    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_post');
    }
}
