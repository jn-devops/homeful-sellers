<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
    //added ggvivar 
    Route::post('forgot-password', [PasswordController::class, 'forgot_password'])->name('password.forgot');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('buyer/match', [BuyerController::class, 'match'])->name('buyer.match');

    Route::post('change-temporary-password', [PasswordController::class, 'change_password_force'])->name('password.change');
});

Route::middleware(['auth', 'verified', 'check.permission:view'])->group(function () {
    // Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers.index');
    Route::get('/leads', [BuyerController::class, 'lead'])->name('leads.index');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::get('/roles', [UserRoleController::class, 'index'])->name('roles.index');
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
});
Route::middleware(['auth', 'verified', 'check.permission:add'])->group(function () {
    Route::post('/buyers', [BuyerController::class, 'store'])->name('buyers.store');
    Route::post('/leads', [BuyerController::class, 'store'])->name('leads.store');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::post('/roles', [UserRoleController::class, 'store'])->name('roles.store');
    Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
});
Route::middleware(['auth', 'verified', 'check.permission:edit'])->group(function () {
    Route::put('/buyers/{buyer}', [BuyerController::class, 'update'])->name('buyers.update');
    Route::put('/leads/{buyer}', [BuyerController::class, 'update'])->name('leads.update');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/agents/{user}', [AgentController::class, 'update'])->name('agents.update');
    Route::put('/roles/{role}', [UserRoleController::class, 'update'])->name('roles.update');
    Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
});
Route::middleware(['auth', 'verified', 'check.permission:delete'])->group(function () {
    Route::delete('/buyers/{buyer}', [BuyerController::class, 'destroy'])->name('buyers.destroy');
    Route::delete('/leads/{buyer}', [BuyerController::class, 'destroy'])->name('leads.destroy');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/agents/{user}', [AgentController::class, 'destroy'])->name('agents.destroy');
    Route::delete('/roles/{role}', [UserRoleController::class, 'destroy'])->name('roles.destroy');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
});
