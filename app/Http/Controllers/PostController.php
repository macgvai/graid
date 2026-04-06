<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        return 'test';
//        return Post::with(['author', 'contentType', 'hashtags'])->paginate(20);
    }

    public function show(Post $post)
    {
        return $post->load(['author', 'contentType', 'hashtags']);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $user_id = Auth::user()->id;

        $data['user_id'] = $user_id;
        $data['content_type_id'] = 1;

        $post = Post::create($data);

        $post->hashtags()->sync($request->input('hashtags', []));

        return response()->json([
            'success' => true,
            'data' => $post
        ], 201);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'text_content' => 'nullable|string',
            'quote_author' => 'nullable|string',
            'image' => 'nullable|string',
            'video' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        $post->update($data);

        if ($request->has('hashtags')) {
            $post->hashtags()->sync($request->hashtags);
        }

        return $post;
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
