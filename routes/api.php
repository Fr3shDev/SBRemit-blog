<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogPostController;
use App\Http\Controllers\API\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::get('/logout', 'logout');
    });

    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(BlogPostController::class)->prefix('blog-posts')->group(function () {
        Route::get('/', 'index');
        Route::get('/published', 'publishedBlogPosts');
        Route::get('/draft', 'draftBlogPosts');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::patch('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
    });
});


