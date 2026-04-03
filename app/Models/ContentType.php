<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    /** @use HasFactory<\Database\Factories\ContentTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_class',
    ];

    /** Посты данного типа */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
