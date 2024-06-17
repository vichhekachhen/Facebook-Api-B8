<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserProfileController;
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

// Admin User routes
Route::prefix('users')->group(function () {
    Route::get('/list', [UserProfileController::class, 'index'])->name('user.profile.list');
    Route::get('/{id}', [UserProfileController::class, 'show']);
    Route::put('/{id}', [UserProfileController::class, 'update']);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

// Post routes
Route::prefix('posts')->group(function () {
    Route::get('/list', [PostController::class, 'index'])->name('posts.index');  //see list post from other
    Route::get('/show/{id}', [PostController::class, 'show'])->name('posts.show');
});


//Login as a user
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/auth/viewProfile', [AuthController::class, 'user'])->name('auth.view');
    Route::put('/auth/update', [AuthController::class, 'updateUser'])->name('auth.update');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');


    // Post routes
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'ownPost'])->name('posts.ownPost');  //see my own post
        Route::post('/create', [PostController::class, 'store'])->name('posts.store');
        Route::put('/update/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/delete/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    });

    //Comment Routes
    Route::group(['prefix' => 'comments'], function () {
        Route::post('/post/{id}', [CommentController::class, 'store'])->name('comments.store');
        Route::put('/update/{id}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });

    //like and unlike routes
    Route::group(['prefix' => 'likes'], function () {
        Route::post('/', [LikeController::class, 'store'])->name('like.store'); 
    });

    //Friend-Request Routes
    Route::post('/friend-requests', [FriendRequestController::class, 'send']);
    Route::post('/friend-requests/{id}/accept', [FriendRequestController::class, 'accept']);
    Route::post('/friend-requests/{id}/decline', [FriendRequestController::class, 'decline']);
});

