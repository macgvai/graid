<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\Posts\PostPublishingService;
use App\Services\Posts\PostViewService;
use App\Services\Posts\RepostPostService;
use App\Services\Posts\YouTubeUrlParser;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request, PostViewService $postViewService): JsonResponse
    {
        /** @var User $viewer */
        $viewer = $request->user();

        return response()->json($postViewService->recent($viewer));
    }

    public function show(Request $request, Post $post, YouTubeUrlParser $youTubeUrlParser): JsonResponse
    {
        $this->incrementViews($post);

        return response()->json([
            'data' => $this->loadDetails($request, $post),
            'youtube_embed_url' => $post->video !== null
                ? $youTubeUrlParser->embedUrl($post->video)
                : null,
        ]);
    }

    public function showPage(Request $request, Post $post, YouTubeUrlParser $youTubeUrlParser): View
    {
        $this->incrementViews($post);
        $post = $this->loadDetails($request, $post);

        return view('pages.post-show', [
            'post' => $post,
            'youtubeEmbedUrl' => $post->video !== null
                ? $youTubeUrlParser->embedUrl($post->video)
                : null,
        ]);
    }

    public function store(
        StorePostRequest $request,
        PostPublishingService $postPublishingService,
    ): JsonResponse|RedirectResponse {
        $postType = $request->postType();

        abort_if($postType === null, 404);

        /** @var User $author */
        $author = $request->user();
        $post = $postPublishingService->publish(
            $author,
            $postType,
            $request->validated(),
            $request->file('image_file'),
        );

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $post,
            ], 201);
        }

        return redirect()->route('posts.show', $post);
    }

    public function repost(
        Request $request,
        Post $post,
        RepostPostService $repostPostService,
    ): JsonResponse|RedirectResponse {
        /** @var User $author */
        $author = $request->user();
        $repost = $repostPostService->repost($author, $post);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $repost,
            ], 201);
        }

        return redirect()->route('users.show', $author);
    }

    private function incrementViews(Post $post): void
    {
        Post::query()->whereKey($post->getKey())->increment('views');
        $post->views++;
    }

    private function loadDetails(Request $request, Post $post): Post
    {
        /** @var User|null $viewer */
        $viewer = $request->user();

        $post->load([
            'author',
            'contentType',
            'hashtags',
            'comments.author',
            'originalAuthor',
            'originalPost.author',
        ])->loadCount(['comments', 'likes', 'reposts']);

        if ($viewer !== null) {
            $post->loadCount([
                'likes as liked_by_viewer' => static function (Builder $query) use ($viewer): void {
                    $query->where('user_id', $viewer->id);
                },
            ]);
        }

        $author = $post->author;
        $author->loadCount(['followers', 'posts']);

        return $post;
    }
}
