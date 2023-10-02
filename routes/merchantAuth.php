<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordlessAuthController;
use App\Http\Controllers\MerchantAuth\PasswordController;
use App\Http\Controllers\CustomVerificationTokenController;
use App\Http\Controllers\MerchantAuth\NewPasswordController;
use App\Http\Controllers\MerchantAuth\VerifyEmailController;
use App\Http\Controllers\MerchantAuth\RegisteredUserController;
use App\Http\Controllers\MerchantAuth\PasswordResetLinkController;
use App\Http\Controllers\MerchantAuth\ConfirmablePasswordController;
use App\Http\Controllers\MerchantAuth\AuthenticatedSessionController;
use App\Http\Controllers\MerchantAuth\EmailVerificationPromptController;
use App\Http\Controllers\MerchantAuth\EmailVerificationNotificationController;
use App\Http\Controllers\OTPController;

Route::middleware('guest:merchant')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    if (config('verification.way') == 'passwordless') {
        Route::post('login', [PasswordlessAuthController::class, 'store']);
        Route::get('verify-email/{merchant}', [PasswordlessAuthController::class, 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('login.verify');
    } elseif (config('verification.way') == 'otp') {
        Route::post('login', [OTPController::class, 'store']);
        Route::post('verify-otp', [OTPController::class, 'verify'])->name('verifyOTP');
    } else {
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    }
});

Route::middleware('merchant')->group(function () {
    if (config('verification.way') == 'email') {
        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
    }

    if (config('verification.way') == 'cvt') {
        Route::get('verify-email', [CustomVerificationTokenController::class, 'notice'])
            ->name('verification.notice');

        Route::get('verify-email/{id}/{token}', [CustomVerificationTokenController::class, 'verify'])
            ->middleware(['throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [CustomVerificationTokenController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
    }

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
