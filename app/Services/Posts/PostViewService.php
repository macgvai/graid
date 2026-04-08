<?php

namespace App\Services\Posts;

use App\Enums\PostType;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostViewService
{
    public function __construct(
        private readonly DatabaseManager $databaseManager,
    ) {
    }

    public function recent(?User $viewer = null, ?PostType $postType = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQuery($viewer, $postType)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function feed(User $viewer, ?PostType $postType = null, int $perPage = 10): LengthAwarePaginator
    {
        $subscriptionQuery = Subscription::query()
            ->select('target_id')
            ->where('author_id', $viewer->id);

        return $this->baseQuery($viewer, $postType)
            ->whereIn('user_id', $subscriptionQuery)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function popular(
        ?User $viewer = null,
        ?PostType $postType = null,
        string $sort = 'popular',
        int $perPage = 10,
    ): LengthAwarePaginator {
        $query = $this->baseQuery($viewer, $postType);

        match ($sort) {
            'likes' => $query->orderByDesc('likes_count')->orderByDesc('id'),
            'date' => $query->latest(),
            default => $query->orderByDesc('views')->orderByDesc('id'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function search(string $rawQuery, ?User $viewer = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = trim($rawQuery);
        $builder = $this->baseQuery($viewer);

        if ($this->isTagSearch($query)) {
            $tag = Str::lower(ltrim(substr($query, 1), '#'));
            $builder->whereHas('hashtags', static function (Builder $hashtagQuery) use ($tag): void {
                $hashtagQuery->where('name', $tag);
            });
        } elseif ($this->databaseManager->connection()->getDriverName() === 'mysql') {
            $builder->whereFullText(['title', 'text_content'], $query);
        } else {
            $builder->where(static function (Builder $textQuery) use ($query): void {
                $textQuery
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('text_content', 'like', "%{$query}%");
            });
        }

        return $builder->latest()->paginate($perPage)->withQueryString();
    }

    public function forUser(User $profileUser, ?User $viewer = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQuery($viewer)
            ->where('user_id', $profileUser->id)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function likedByUser(User $profileUser, ?User $viewer = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->baseQuery($viewer)
            ->whereHas('likes', static function (Builder $likeQuery) use ($profileUser): void {
                $likeQuery->where('user_id', $profileUser->id);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    private function baseQuery(?User $viewer = null, ?PostType $postType = null): Builder
    {
        $query = Post::query()
            ->with([
                'author',
                'contentType',
                'hashtags',
                'originalAuthor',
                'originalPost.author',
            ])
            ->withCount(['comments', 'likes', 'reposts']);

        if ($viewer !== null) {
            $query->withCount([
                'likes as liked_by_viewer' => static function (Builder $likeQuery) use ($viewer): void {
                    $likeQuery->where('user_id', $viewer->id);
                },
            ]);
        }

        if ($postType !== null) {
            $query->where('content_type_id', $postType->value);
        }

        return $query;
    }

    private function isTagSearch(string $query): bool
    {
        return $query !== '' && substr($query, 0, 1) === '#';
    }
}
