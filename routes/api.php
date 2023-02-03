<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TemaController;
use Illuminate\Support\Facades\Auth;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/temas/buscar', [TemaController::class,'index']);
    Route::get('/tema/{id}',[TemaController::class,'show']);
    Route::post('/tema/store',[TemaController::class,'store']);
});

// Auth::routes();
// Route::middleware(['auth:sanctum'])->group(function () {
//     // 
// });
// Route::post('/login',[LoginController::class,'login']);
// Route::get('/login/google',[LoginController::class,'redirectToProvider']);
Route::get('/prueba', function () {
    return ['valo' => true];
});
