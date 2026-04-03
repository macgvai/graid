<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    public function index()
    {
        return Post::with(['author', 'contentType', 'hashtags'])->paginate(20);
    }

    public function show(Post $post)
    {
        return $post->load(['author', 'contentType', 'hashtags']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content_type_id' => 'required|exists:content_types,id',
            'title' => 'nullable|string',
            'text_content' => 'nullable|string',
            'quote_author' => 'nullable|string',
            'image' => 'nullable|string',
            'video' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        $post = Post::create($data);

        if ($request->has('hashtags')) {
            $post->hashtags()->sync($request->hashtags);
        }

        return response()->json($post, 201);
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
