<?php

use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\LocationCheckController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Models\SisKunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/generate', function(){
   Artisan::call('storage:link');
   echo 'ok';
});

Route::middleware(ApiAuthMiddleware::class)->group(function() {
    
    Route::controller(KunjunganController::class)->group(function(){
        Route::post('/kunjungan' ,'saveKunjungan');
        Route::get('/kunjungan/top' ,'topKunjungan');
        Route::get('/kunjungan/group/{id}' ,'groupKunjungan');
        Route::get('/kunjungan/history' ,'getKunjungan');
        Route::delete('/kunjungan/delete/{id}' ,'deleteKunjungan');
        Route::get('/project' ,'getProject');
    });
    Route::controller(LocationCheckController::class)->group(function(){
        Route::post('/check-location' ,'check');
    });
    

    
});

