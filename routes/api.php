<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users', [AuthController::class, 'index']);

    Route::get('/users/{id}', [AuthController::class, 'getuser']);


    Route::delete('/users/{id}', [AuthController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/profile/{id}', [AuthController::class, 'profile']);

    Route::resource('/messages', MessageController::class);

    Route::get('/dashboard', [DashboardController::class, 'index']);

});

//Route::resource('/messages', MessageController::class);
//Route::put('/profile/{id}', [AuthController::class, 'profile']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

