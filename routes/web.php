<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Обычные страницы
Route::middleware('auth:sanctum')->group(function () {
    Route::view('/', 'pages.main')->name('main');
    Route::view('/feed', 'pages.feed')->name('feed');
    Route::view('/feed/empty', 'pages.no-content')->name('no-content');
    Route::view('/messages', 'pages.messages')->name('messages');
    Route::view('/search/empty', 'pages.no-results')->name('no-results');
    Route::view('/popular', 'pages.popular')->name('popular');
    Route::view('/posts/create', 'pages.adding-post')->name('adding-post');
    Route::view('/posts/show', 'pages.post-details')->name('post-details');
    Route::view('/profile', 'pages.profile')->name('profile');
    Route::view('/search', 'pages.search-results')->name('search-results');
    Route::view('/modal', 'pages.modal')->name('modal');
    Route::view('/validation/register', 'pages.reg-validation')->name('reg-validation');
    Route::view('/validation/login', 'pages.login-validation')->name('login-validation');
    Route::view('/pages', 'pages.index')->name('pages-index');
});


// Регистрация
Route::view('/register', 'pages.registration')->name('registration');
Route::post('/register', [UserController::class, 'register'])->name('registration.store');

Route::view('/login', 'pages.login')->name('login');
Route::post('/login', [UserController::class, 'login'])->name('web.login');

