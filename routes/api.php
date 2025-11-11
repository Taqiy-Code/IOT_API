<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceSettingController;
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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']); // Contoh rute terproteksi

    Route::apiResource('devices', DeviceController::class);

    Route::get('/devices/{device}/settings', [DeviceSettingController::class, 'show']);
    Route::put('/devices/{device}/settings', [DeviceSettingController::class, 'update']);

    Route::get('/devices/{device}/alerts', [AlertController::class, 'index']);
    Route::post('/devices/{device}/alerts', [AlertController::class, 'store']);

    Route::put('/alerts/{alert}/read', [AlertController::class, 'markAsRead']);
});


Route::get('/reset-password/{token}', function ($token) {
    abort(404);
})->name('password.reset');