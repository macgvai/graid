<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::post('/register', [UserController::class, 'register'])->name('register ');



Route::get('/posts', [PostController::class, 'index']); // список постов
Route::get('/posts/{id}', [PostController::class, 'show']); // один пост
