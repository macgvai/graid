<?php

$root = __DIR__;
$viewsDir = $root . '/resources/views/pages';

$routeMap = [
    'main.html' => 'main',
    'login.html' => 'login',
    'feed.html' => 'feed',
    'no-content.html' => 'no-content',
    'messages.html' => 'messages',
    'no-results.html' => 'no-results',
    'popular.html' => 'popular',
    'post-details.html' => 'post-details',
    'profile.html' => 'profile',
    'search-results.html' => 'search-results',
    'registration.html' => 'registration',
    'adding-post.html' => 'adding-post',
    'modal.html' => 'modal',
    'reg-validation.html' => 'reg-validation',
    'login-validation.html' => 'login-validation',
    'index.html' => 'pages-index',
];

foreach ($routeMap as $file => $routeName) {
    $content = file_get_contents($root . '/public/' . $file);

    foreach ($routeMap as $innerFile => $innerRoute) {
        $content = str_replace('href="' . $innerFile . '"', 'href="{{ route(\'' . $innerRoute . '\') }}"', $content);
        $content = str_replace('action="' . $innerFile . '"', 'action="{{ route(\'' . $innerRoute . '\') }}"', $content);
    }

    $patterns = [
        '/(<link[^>]+href=")((?:css|js|img|fonts|libs)\/[^"]+)(")/u',
        '/(<script[^>]+src=")((?:css|js|img|fonts|libs)\/[^"]+)(")/u',
        '/(<img[^>]+src=")((?:css|js|img|fonts|libs)\/[^"]+)(")/u',
        '/(<source[^>]+src=")((?:css|js|img|fonts|libs)\/[^"]+)(")/u',
    ];

    foreach ($patterns as $pattern) {
        $content = preg_replace($pattern, '$1{{ asset(\'$2\') }}$3', $content);
    }

    $targetName = $file === 'index.html'
        ? 'index.blade.php'
        : basename($file, '.html') . '.blade.php';

    file_put_contents($viewsDir . '/' . $targetName, $content);
}
