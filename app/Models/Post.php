<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $original_post_id
 * @property int|null $original_author_id
 * @property int $content_type_id
 * @property string $title
 * @property string|null $text_content
 * @property string|null $quote_author
 * @property string|null $image
 * @property string|null $video
 * @property string|null $link
 * @property string|null $link_preview
 * @property bool $is_repost
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $author
 * @property-read Post|null $originalPost
 * @property-read User|null $originalAuthor
 * @property-read ContentType $contentType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Like> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Hashtag> $hashtags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $reposts
 * @property-read int|null $comments_count
 * @property-read int|null $likes_count
 * @property-read int|null $reposts_count
 * @property-read int|null $liked_by_viewer
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_post_id',
        'original_author_id',
        'content_type_id',
        'title',
        'text_content',
        'quote_author',
        'image',
        'video',
        'link',
        'link_preview',
        'is_repost',
        'views',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'is_repost' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function originalPost(): BelongsTo
    {
        return $this->belongsTo(self::class, 'original_post_id');
    }

    public function originalAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_author_id');
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_post')->withTimestamps();
    }

    public function reposts(): HasMany
    {
        return $this->hasMany(self::class, 'original_post_id');
    }
}
