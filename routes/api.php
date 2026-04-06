<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::post('/register', [UserController::class, 'register'])->name('register ');
Route::post('/login', [UserController::class, 'login'])->name('login ');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store'); //
    Route::get('/posts/{id}', [PostController::class, 'show']);
});



