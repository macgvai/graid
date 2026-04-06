<?php

use Illuminate\Support\Facades\Route;

$pages = [
    '/' => ['view' => 'pages.main', 'name' => 'main'],
    '/login' => ['view' => 'pages.login', 'name' => 'login'],
    '/feed' => ['view' => 'pages.feed', 'name' => 'feed'],
    '/feed/empty' => ['view' => 'pages.no-content', 'name' => 'no-content'],
    '/messages' => ['view' => 'pages.messages', 'name' => 'messages'],
    '/search/empty' => ['view' => 'pages.no-results', 'name' => 'no-results'],
    '/popular' => ['view' => 'pages.popular', 'name' => 'popular'],
    '/posts/create' => ['view' => 'pages.adding-post', 'name' => 'adding-post'],
    '/posts/show' => ['view' => 'pages.post-details', 'name' => 'post-details'],
    '/profile' => ['view' => 'pages.profile', 'name' => 'profile'],
    '/search' => ['view' => 'pages.search-results', 'name' => 'search-results'],
    '/register' => ['view' => 'pages.registration', 'name' => 'registration'],
    '/modal' => ['view' => 'pages.modal', 'name' => 'modal'],
    '/validation/register' => ['view' => 'pages.reg-validation', 'name' => 'reg-validation'],
    '/validation/login' => ['view' => 'pages.login-validation', 'name' => 'login-validation'],
    '/pages' => ['view' => 'pages.index', 'name' => 'pages-index'],
];

foreach ($pages as $uri => $page) {
    Route::view($uri, $page['view'])->name($page['name']);
}

$legacyRedirects = [
    '/main.html' => 'main',
    '/login.html' => 'login',
    '/feed.html' => 'feed',
    '/no-content.html' => 'no-content',
    '/messages.html' => 'messages',
    '/no-results.html' => 'no-results',
    '/popular.html' => 'popular',
    '/adding-post.html' => 'adding-post',
    '/post-details.html' => 'post-details',
    '/profile.html' => 'profile',
    '/search-results.html' => 'search-results',
    '/registration.html' => 'registration',
    '/modal.html' => 'modal',
    '/reg-validation.html' => 'reg-validation',
    '/login-validation.html' => 'login-validation',
    '/index.html' => 'pages-index',
];

foreach ($legacyRedirects as $uri => $routeName) {
    Route::get($uri, static fn () => redirect()->route($routeName, status: 301))->name("legacy.$routeName");
}
