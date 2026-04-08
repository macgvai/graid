<?php

namespace App\Services\Posts;

use Illuminate\Support\Str;

class YouTubeUrlParser
{
    public function extractVideoId(string $url): ?string
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ! isset($parts['host'])) {
            return null;
        }

        $host = Str::lower($parts['host']);
        $videoId = null;

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = trim($parts['path'] ?? '', '/');
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            parse_str($parts['query'] ?? '', $query);

            if (isset($query['v']) && is_string($query['v'])) {
                $videoId = $query['v'];
            } else {
                $pathParts = explode('/', trim($parts['path'] ?? '', '/'));

                if (($pathParts[0] ?? null) === 'shorts' && isset($pathParts[1])) {
                    $videoId = $pathParts[1];
                }
            }
        }

        if (! is_string($videoId) || $videoId === '') {
            return null;
        }

        return $videoId;
    }

    public function embedUrl(string $url): ?string
    {
        $videoId = $this->extractVideoId($url);

        if ($videoId === null) {
            return null;
        }

        return sprintf('https://www.youtube.com/embed/%s', $videoId);
    }
}
