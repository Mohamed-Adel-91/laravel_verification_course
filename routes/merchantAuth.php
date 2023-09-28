<?php

use App\Http\Controllers\MerchantAuth\AuthenticatedSessionController;
use App\Http\Controllers\MerchantAuth\ConfirmablePasswordController;
use App\Http\Controllers\MerchantAuth\EmailVerificationNotificationController;
use App\Http\Controllers\MerchantAuth\EmailVerificationPromptController;
use App\Http\Controllers\MerchantAuth\NewPasswordController;
use App\Http\Controllers\MerchantAuth\PasswordController;
use App\Http\Controllers\MerchantAuth\PasswordResetLinkController;
use App\Http\Controllers\MerchantAuth\RegisteredUserController;
use App\Http\Controllers\MerchantAuth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:merchant')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Route::get('verify-email', EmailVerificationPromptController::class)
    //     ->name('verification.notice');

    // Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    //     ->middleware(['signed', 'throttle:6,1'])
    //     ->name('verification.verify');

    // Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    //     ->middleware('throttle:6,1')
    //     ->name('verification.send');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
