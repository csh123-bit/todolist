<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::prefix('user')->group(function(){
    Route::post('register', [UserController::class,'register']);
    Route::post('login',[UserController::class,'login']);

    Route::middleware(['auth:api'])->group(function(){
        Route::get('/',[UserController::class,'user']);
        Route::get('logout', [UserController::class,'logout']);

        Route::resource('todos', TodoController::class);
    });
});
