<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New subscriber</title>
</head>
<body>
    <p>Hello, {{ $recipient->login }}!</p>
    <p>{{ $subscriber->login }} subscribed to your profile.</p>
    <p><a href="{{ $subscriberProfileUrl }}">Open subscriber profile</a></p>
</body>
</html>
