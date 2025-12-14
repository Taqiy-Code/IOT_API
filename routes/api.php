<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceSettingController;
use App\Http\Controllers\Lighting\LightController;
use App\Http\Controllers\Temperature\TemperatureRealtimeController;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::get('/tes-mqtt/{msg}', function ($msg) {
    $mqtt = new MqttService();
    $mqtt->publish('esp/lampu/control', $msg);

    return response()->json([
        'status' => true,
        'message' => "MQTT terkirim: $msg"
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);

// Route::middleware('auth:sanctum')->group(function () {
Route::post('logout', [AuthController::class, 'logout']);
Route::get('user', [AuthController::class, 'user']); // Contoh rute terproteksi

Route::apiResource('devices', DeviceController::class);



Route::get('/devices/{device}/settings', [DeviceSettingController::class, 'show']);
Route::put('/devices/{device}/settings', [DeviceSettingController::class, 'update']);

Route::get('/devices/{device}/alerts', [AlertController::class, 'index']);
Route::post('/devices/{device}/alerts', [AlertController::class, 'store']);

Route::put('/alerts/{alert}/read', [AlertController::class, 'markAsRead']);
// });


Route::get('/reset-password/{token}', function ($token) {
    abort(404);
})->name('password.reset');

Route::get('devices/{device_code}', [DeviceController::class, 'showByCode']);

// Temperature
Route::post('/temperature/realtime', [TemperatureRealtimeController::class, 'store']);
Route::get('/uji-tampilan', [TemperatureRealtimeController::class, 'index']);
// Lighting
Route::prefix('lighting/{device_code}')->group(function () {
    Route::get('/status', [LightController::class, 'status']);
    Route::post('/mode', [LightController::class, 'setMode']);
    Route::post('/manual', [LightController::class, 'manualControl']);
    Route::post('/config', [LightController::class, 'updateConfig']);
});
