<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLikeRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\Likes\PostLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class LikeController extends Controller
{
    public function store(
        StoreLikeRequest $request,
        Post $post,
        PostLikeService $postLikeService,
    ): JsonResponse|RedirectResponse {
        /** @var User $user */
        $user = $request->user();
        $like = $postLikeService->like($user, $post);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $like,
            ], 201);
        }

        return redirect()->back();
    }
}
