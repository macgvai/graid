<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New post published</title>
</head>
<body>
    <p>Hello, {{ $recipient->login }}!</p>
    <p>{{ $post->author->login }} published a new post.</p>
    <p>Title: {{ $post->title }}</p>
    <p><a href="{{ route('posts.show', $post) }}">Open post</a></p>
    <p><a href="{{ $authorProfileUrl }}">Open author profile</a></p>
</body>
</html>
