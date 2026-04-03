<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id',
        'target_id',
    ];

    /** Подписавшийся */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** На кого подписались */
    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }

}
