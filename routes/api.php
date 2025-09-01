<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ConfirmEmailController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SendVerifyCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\AddressController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// -------------- Auth -------------- //
Route::post('/auth/signup', [SignupController::class, 'index']);
Route::post('/auth/send_verifycode', [SendVerifyCodeController::class, 'index']);
Route::post('/auth/login', [LoginController::class, 'index']);
Route::post('/auth/confirm_email', [ConfirmEmailController::class, 'index']);
Route::post('/auth/logout', [LogoutController::class, 'index'])->middleware(['auth:sanctum']);

// -------------- Forgot Password -------------- //
Route::post('/auth/forgot_password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/auth/verify_code', [ForgotPasswordController::class, 'verifyCode']);
Route::post('/auth/reset_password', [ForgotPasswordController::class, 'resetPassword']);

// -------------- Home -------------- //
Route::post('/home', [HomeController::class, 'index']);

// -------------- Categories -------------- //
Route::apiResource('categories', CategoriesController::class);

// -------------- Items -------------- //
Route::apiResource('items', ItemsController::class);
Route::post('/category_items/{id?}', [ItemsController::class, 'categoryItems']);
Route::post('/items/search/{id?}', [ItemsController::class, 'searchItems']);

// -------------- Favorites -------------- //
Route::post('/favorites', [FavoritesController::class, 'index']);
Route::post('/favorites/add_or_remove', [FavoritesController::class, 'addOrRemove']);

// -------------- Cart -------------- //
Route::post('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::delete('/cart/remove', [CartController::class, 'remove']);
Route::post('/cart/decrease', [CartController::class, 'decrease']);
Route::post('/cart/increase', [CartController::class, 'increase']);
Route::post('/cart/check_coupon', [CartController::class, 'checkCoupon']);

Route::middleware('auth:sanctum')->group(function () {

    // -------------- Address -------------- //
    Route::post('/address', [AddressController::class, 'index']);
    Route::post('/address/add', [AddressController::class, 'store']);
    Route::post('/address/show/{id}', [AddressController::class, 'show']);
    Route::put('/address/update/{id}', [AddressController::class, 'update']);
    Route::delete('/address/delete/{id}', [AddressController::class, 'destroy']);
});
