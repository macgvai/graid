<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Models\User;
use App\Services\Posts\PostViewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request, PostViewService $postViewService): View|JsonResponse
    {
        /** @var User $viewer */
        $viewer = $request->user();
        $activeType = PostType::tryFrom((int) $request->query('type'));
        $posts = $postViewService->feed($viewer, $activeType);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($posts);
        }

        return view('pages.feed', [
            'posts' => $posts,
            'activeType' => $activeType,
        ]);
    }
}
