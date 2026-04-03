<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']); // список постов
Route::get('/posts/{id}', [PostController::class, 'show']); // один пост
