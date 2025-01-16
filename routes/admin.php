<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
//use App\Http\Controllers\Admin\UserController;  
Route::namespace('Admin\Auth')->group(function () {

    Route::get('/run-artisan-commands', [App\Http\Controllers\HomeController::class, 'runArtisanCommands']);           
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
});
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth:admin');
// // User Resource Routes (Admin Only)
// Route::middleware('auth:admin')->group(function () {
//     Route::resource('users', UserController::class);
// });
Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //Route::resource('users', UserController::class);
});
Route::namespace('Admin\Auth')->group(function () {    
    
    Route::post('login', [AdminLoginController::class, 'login']);
    // Password Reset Routes
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
    Route::post('password/reset', [ForgotPasswordController::class, 'sendResetCodeEmail']);
    Route::get('password/code-verify', [ForgotPasswordController::class, 'codeVerify'])->name('password.code.verify');
    Route::post('password/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify.code');

    // Reset Password Form
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('password/reset/change', [ResetPasswordController::class, 'reset'])->name('password.change');
});