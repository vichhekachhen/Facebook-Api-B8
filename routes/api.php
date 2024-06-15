<?php


use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
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

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/user', [AuthController::class, 'user'])->name('auth.user');

     // User routes
    Route::prefix('users')->group(function () {
        Route::get('/list', [UserProfileController::class, 'index'])->name('user.profile.list');
        Route::get('/{id}', [UserProfileController::class, 'show']);
        Route::put('/{id}', [UserProfileController::class, 'update']);
        Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('user.profile.delete');
    });

    // Post routes
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.index');
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::get('/{id}', [PostController::class, 'show'])->name('posts.show');
        Route::put('/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    });

    //Comment routes
    Route::group(['prefix' => 'posts/{postId}/comments'], function () {
        Route::post('/', [CommentController::class, 'store'])->name('comments.store');
    });
    Route::group(['prefix' => 'comments'], function () {
        Route::put('/{id}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });

    // Like Routes
    Route::group(['prefix' => 'likes'], function () {
        Route::get('/list', [LikeController::class, 'index'])->name('like.list');
        Route::post('/', [LikeController::class, 'store'])->name('like.create');
        Route::delete('unlike', [LikeController::class, 'unlike'])->name('like.unlike');
    });


    // Friend Request Routes
    Route::prefix('friend-requests')->group(function () {
        // Friend Request Routes
        Route::get('/', [FriendRequestController::class, 'index'])->middleware('auth:api');
        Route::post('/', [FriendRequestController::class, 'store'])->middleware('auth:api');
        Route::put('//{id}/accept', [FriendRequestController::class, 'accept'])->middleware('auth:api');
        Route::put('//{id}/decline', [FriendRequestController::class, 'decline'])->middleware('auth:api');
    });
    
});
