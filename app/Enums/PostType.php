<?php

namespace App\Enums;

enum PostType: int
{
    case Photo = 1;
    case Video = 2;
    case Text = 3;
    case Quote = 4;
    case Link = 5;

    public function label(): string
    {
        return match ($this) {
            self::Photo => 'Фото',
            self::Video => 'Видео',
            self::Text => 'Текст',
            self::Quote => 'Цитата',
            self::Link => 'Ссылка',
        };
    }

    public function iconClass(): string
    {
        return match ($this) {
            self::Photo => 'photo',
            self::Video => 'video',
            self::Text => 'text',
            self::Quote => 'quote',
            self::Link => 'link',
        };
    }
}
