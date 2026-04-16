<?php

use App\Http\Controllers\Api\ApiGaleriController;
use App\Http\Controllers\Api\ApiLoginController;
use App\Http\Controllers\Api\ApiPesanAmbulanceController;
use App\Http\Controllers\Api\AuthOtpController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiLoginController::class, 'login']);
Route::post('/send-otp', [AuthOtpController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthOtpController::class, 'verifyOtp']);
Route::get('/galeri_foto', [ApiGaleriController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiLoginController::class, 'logout']);
    Route::post('/pesan-ambulance/store', [ApiPesanAmbulanceController::class, 'store']);
    Route::get('/daftar-pesan-ambulance', [ApiPesanAmbulanceController::class, 'index']);
    Route::get('/pesan-ambulance', [ApiPesanAmbulanceController::class, 'pesan_ambulance']);
    Route::post('/pesan-ambulance/store', [ApiPesanAmbulanceController::class, 'store']);
    Route::get('/data', [ApiPesanAmbulanceController::class, 'data']);
    Route::put('/batal-pesan/{id}', [ApiPesanAmbulanceController::class, 'batal_pesan']);
    Route::get('/summary-ambulance', [ApiPesanAmbulanceController::class, 'getDashboardSummary']);
});
