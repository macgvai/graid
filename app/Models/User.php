<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
        'login',
        'email',
        'password',
        'avatar',
        'registered_at',
    ];

    /** Посты пользователя */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /** Комментарии пользователя */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Лайки пользователя */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /** На кого он подписан */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'author_id');
    }

    /** Его подписчики */
    public function followers()
    {
        return $this->hasMany(Subscription::class, 'target_id');
    }

    /** Отправленные сообщения */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /** Полученные сообщения */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
