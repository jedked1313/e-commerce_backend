<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SigupController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ConfirmEmailController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/signup',[SigupController::class,'index']);
Route::post('/auth/login',[LoginController::class,'index']);
Route::post('/auth/confirm_email',[ConfirmEmailController::class,'index']);

Route::post('/auth/forgot_password',[ForgotPasswordController::class,'forgotPassword']);
Route::post('/auth/verify_code',[ForgotPasswordController::class,'verifyCode']);
Route::post('/auth/reset_password',[ForgotPasswordController::class,'resetPassword']);
// Route::get('/sendemail',[EmailController::class, 'send'])->middleware(['auth:sanctum']);