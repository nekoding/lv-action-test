<?php

use App\Actions\Auth\ConfirmPassword;
use App\Actions\Auth\ForgotPassword;
use App\Actions\Auth\Login;
use App\Actions\Auth\Logout;
use App\Actions\Auth\Register;
use App\Actions\Auth\ResetPassword;
use App\Actions\Auth\ResetPasswordForm;
use App\Actions\Auth\SendEmailVerification;
use App\Actions\Auth\UpdatePassword;
use App\Actions\Auth\VerifyEmailNotice;
use App\Actions\Auth\VerifyEmailUser;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('register', 'auth.register')->name('register');
    Route::post('register', Register::class);

    Route::view('login', 'auth.login')->name('login');
    Route::post('login', Login::class);

    Route::view('forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('forgot-password', ForgotPassword::class)->name('password.email');

    Route::get('reset-password/{token}', ResetPasswordForm::class)->name('password.reset');
    Route::post('reset-password', ResetPassword::class)->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmailNotice::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailUser::class)->name('verification.verify');

    Route::post('email/verification-notification', SendEmailVerification::class)->name('verification.send');

    Route::view('confirm-password', 'auth.confirm-password')->name('password.confirm');
    Route::post('confirm-password', ConfirmPassword::class);

    Route::put('password', UpdatePassword::class)->name('password.update');

    Route::post('logout', Logout::class)->name('logout');
});
