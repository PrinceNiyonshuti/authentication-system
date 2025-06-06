<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegistrationController;

Route::post('/register/step1', [RegistrationController::class, 'step1']);
Route::post('/register/step2', [RegistrationController::class, 'step2']);
Route::post('/register/send-otp', [RegistrationController::class, 'sendOtp']);
Route::post('/register/verify-otp', [RegistrationController::class, 'verifyOtp']);
Route::post('/register/step4', [RegistrationController::class, 'step4']);



Route::get('/register/step1', function () {
    return dd('test');
});
