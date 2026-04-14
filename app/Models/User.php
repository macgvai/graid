<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Like> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subscription> $subscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subscription> $followers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followedUsers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $subscribers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $likedPosts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Message> $sentMessages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Message> $receivedMessages
 * @property-read int|null $followers_count
 * @property-read int|null $posts_count
 */
class User extends Authenticatable
{
    /** @use HasApiTokens<\Laravel\Sanctum\PersonalAccessToken> */
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    protected $fillable = [
        'login',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'author_id');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Subscription::class, 'target_id');
    }

    public function followedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'author_id', 'target_id');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'target_id', 'author_id');
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'likes')->withTimestamps();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
