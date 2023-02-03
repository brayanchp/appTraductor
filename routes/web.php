<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/obtenerusuario',function(){
    dd(Auth::user());
  $user= Auth::loginUsingId(12);
  dd($user);
    dd('hecho');
});

// Auth::routes();
Route::post('login',[LoginController::class,'autenticate']);
Route::get('/logout',[LoginController::class,'logout']);
// Route::get('/login/google',[LoginController::class,'redirectToProvider']);
 
// Route::get('/login/google/callback',[LoginController::class,'handleProviderCallback']);
// Route::middleware(['auth:sanctum'])->group(function () {
    // Route::post('/login',[LoginController::class,'authenticate']);
// });

// Route::get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
