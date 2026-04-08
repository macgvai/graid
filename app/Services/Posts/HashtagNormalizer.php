<?php

namespace App\Services\Posts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HashtagNormalizer
{
    public function isValid(string $tags): bool
    {
        return preg_match('/^\S+(?:\s+\S+)*$/u', $tags) === 1;
    }

    /**
     * @return array<int, string>
     */
    public function normalize(string $tags): array
    {
        return $this->split($tags)
            ->map(static fn (string $tag): string => ltrim(Str::lower($tag), '#'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, string>
     */
    private function split(string $tags): Collection
    {
        /** @var array<int, string> $chunks */
        $chunks = preg_split('/\s+/u', trim($tags), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($chunks);
    }
}
