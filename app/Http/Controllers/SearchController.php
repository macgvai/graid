<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Models\Post;
use App\Models\User;
use App\Services\Posts\PostViewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, PostViewService $postViewService): View|JsonResponse
    {
        /** @var User|null $viewer */
        $viewer = $request->user();
        $query = trim((string) $request->query('query', ''));

        $posts = $query === ''
            ? Post::query()->where('id', 0)->paginate(10)->withQueryString()
            : $postViewService->search($query, $viewer);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'query' => $query,
                'data' => $posts,
            ]);
        }

        return view('pages.search-results', [
            'query' => $query,
            'posts' => $posts,
        ]);
    }

    public function popular(Request $request, PostViewService $postViewService): View|JsonResponse
    {
        /** @var User|null $viewer */
        $viewer = $request->user();
        $sort = (string) $request->query('sort', 'popular');
        $activeType = PostType::tryFrom((int) $request->query('type'));
        $posts = $postViewService->popular($viewer, $activeType, $sort);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($posts);
        }

        return view('pages.popular', [
            'posts' => $posts,
            'activeType' => $activeType,
            'sort' => $sort,
        ]);
    }
}
