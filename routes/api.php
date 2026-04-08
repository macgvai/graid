<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register'])->name('api.register');
Route::post('/login', [UserController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [UserController::class, 'logout'])->name('api.logout');

    Route::get('/feed', [FeedController::class, 'index'])->name('api.feed.index');
    Route::get('/popular', [SearchController::class, 'popular'])->name('api.popular.index');
    Route::get('/search', [SearchController::class, 'index'])->name('api.search.index');

    Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
    Route::post('/posts/{type}', [PostController::class, 'store'])->name('api.posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('api.posts.show');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('api.comments.store');
    Route::post('/posts/{post}/likes', [LikeController::class, 'store'])->name('api.likes.store');
    Route::post('/posts/{post}/repost', [PostController::class, 'repost'])->name('api.posts.repost');

    Route::get('/users/{user}', [UserController::class, 'show'])->name('api.users.show');
    Route::post('/users/{user}/subscribe', [SubscriptionController::class, 'store'])->name('api.subscriptions.store');
    Route::delete('/users/{user}/subscribe', [SubscriptionController::class, 'destroy'])
        ->name('api.subscriptions.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('api.messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('api.messages.store');
});
