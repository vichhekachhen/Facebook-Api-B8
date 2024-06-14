<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('user', [AuthController::class, 'user'])->name('auth.user');
    });
});

//user
Route::get('user/list', [UserController::class, 'index'])->name('user.list');
Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
Route::put('user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::get('user/show/{id}', [UserController::class, 'show'])->name('user.show');

//comment
Route::get('comment/list', [CommentController::class, 'index'])->name('comment.list');
Route::post('comment/create', [CommentController::class, 'store'])->name('comment.create');
Route::delete('comment/delete/{id}', [CommentController::class, 'destroy'])->name('comment.delete');
Route::put('comment/update/{id}', [CommentController::class, 'update'])->name('comment.update');
Route::get('comment/show/{id}', [CommentController::class, 'show'])->name('comment.show');


//posts
Route::get('/post/list',[PostController::class,'index'])->name('post.list');
Route::post('/post/create',[PostController::class,'store'])->name('post.create');
Route::put('/post/update/{id}',[PostController::class,'update'])->name('post.update');
Route::delete('/post/delete/{id}',[PostController::class,'destroy'])->name('post.destroy');

// Likes routes
Route::prefix('like')->group(function () {
    Route::get('/list', [LikeController::class, 'index'])->name('like.list');
    Route::get('/show/{id}', [LikeController::class, 'show'])->name('like.show');
    Route::post('/create', [LikeController::class, 'store'])->name('like.create');
    Route::put('/update/{id}', [LikeController::class, 'update'])->name('like.update');
    Route::delete('/delete/{id}', [LikeController::class, 'destroy'])->name('like.destroy');
});

