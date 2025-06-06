<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegistrationController;

Route::post('/register/step1', [RegistrationController::class, 'step1']);
Route::get('/register/step1', function () {
    return dd('test');
});
