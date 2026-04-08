<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\Comments\CommentPublishingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(
        StoreCommentRequest $request,
        Post $post,
        CommentPublishingService $commentPublishingService,
    ): JsonResponse|RedirectResponse {
        /** @var User $author */
        $author = $request->user();

        $comment = $commentPublishingService->publish(
            $author,
            $post,
            (string) $request->validated('content'),
        );

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $comment,
            ], 201);
        }

        return redirect()->route('users.show', $post->author);
    }
}
