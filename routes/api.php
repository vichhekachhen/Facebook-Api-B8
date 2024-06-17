<?php

use App\Http\Controllers\Api\MediaController;
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

// register/login/logout/user
Route::group(['prefix' => 'auth'], function () {
  Route::post('register', [AuthController::class, 'register']);
  Route::post('login', [AuthController::class, 'login']);

  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
  });
});

//user
Route::get('user/list', [UserController::class, 'index'])->name('user.list');
Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
Route::put('user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::get('user/show/{id}', [UserController::class, 'show'])->name('user.show');


// Posts routes
Route::prefix('post')->group(function () {
    Route::get('/list', [PostController::class, 'index']);
    Route::post('/create', [PostController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/update/{id}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/delete/{id}', [PostController::class, 'destroy'])->name('post.destroy');
});

//Medias
Route::post('/photos/create', [MediaController::class, 'store']);

