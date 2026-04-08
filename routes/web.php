<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('main');

Route::middleware('guest')->group(function (): void {
    Route::view('/register', 'pages.registration')->name('registration');
    Route::post('/register', [UserController::class, 'register'])->name('registration.store');

    Route::view('/login', 'pages.login')->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('web.login');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');
    Route::get('/popular', [SearchController::class, 'popular'])->name('popular');
    Route::get('/search', [SearchController::class, 'index'])->name('search-results');

    Route::view('/posts/create', 'pages.adding-post')->name('adding-post');
    Route::post('/posts/{type}', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'showPage'])->name('posts.show');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/posts/{post}/likes', [LikeController::class, 'store'])->name('likes.store');
    Route::post('/posts/{post}/repost', [PostController::class, 'repost'])->name('posts.repost');

    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/users/{user}/subscribe', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::delete('/users/{user}/subscribe', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
