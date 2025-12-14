<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lighting/{device_code}', function ($device_code) {
    return view('lighting.dashboard', ['device_code' => $device_code]);
});

