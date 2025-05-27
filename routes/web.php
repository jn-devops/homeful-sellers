<?php

use App\Http\Controllers\SyncProjectsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BuyerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Homeful\Contacts\Contacts;

use Homeful\Contacts\Models\Customer as Contact;
// Route::get('/', function () {
//     return redirect()->route('voucher.create');
// });

Route::get('/dashboard', function () {
    $user = Auth::user();
    $seller_email = $user->email;
    $seller_comm_code = $user->meta->seller_commission_code;
    // $buyers = Contact::where('seller_email',  $seller_email)
    // ->where('seller_commission_code',  $seller_comm_code)->get();
    $buyers = Contact::all();
    

    return Inertia::render('Dashboard', ['buyers' => $buyers]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('buyer/register', [BuyerController::class, 'edit'])->middleware(['auth', 'verified'])->name('buyer.edit');
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
