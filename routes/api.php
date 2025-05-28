<?php

use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\UserController;
use App\Models\SisKunjungan;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(UserController::class)->group(function(){
    Route::post('/users/login' ,'loginUser');
    Route::post('/users/logout', 'logout');
    Route::post('/users/register' ,'register');
});

Route::controller(KunjunganController::class)->group(function(){
    Route::post('/kunjungan' ,'saveKunjungan');
});
