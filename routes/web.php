<?php

use App\Http\Controllers\SyncProjectsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ProjectController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('voucher.create');
});

// Depricated
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('gratis-from-3neti')->group(function () {


});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

Route::resource('voucher', VoucherController::class)->middleware(['auth', 'verified'])->only(['create', 'store']);
Route::resource('projects', ProjectController::class);
Route::resource('sync-projects', SyncProjectsController::class)->only(['create', 'store']);

require __DIR__.'/auth.php';
