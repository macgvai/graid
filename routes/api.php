<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store'); //
    Route::get('/posts/{id}', [PostController::class, 'show']);
});



