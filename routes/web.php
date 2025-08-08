<?php

use App\Http\Controllers\SyncProjectsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BuyerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Homeful\Contacts\Contacts;
use Illuminate\Support\Facades\Http;
       

use Homeful\Contacts\Models\Customer as Contact;

Route::get('/admin/dashboard', function(){


});
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    $buyers = Contact::where('landline', $user->email . "/" . $user->meta->seller_commission_code)
        // ->whereNull('current_status')
        ->orderBy('created_at', 'desc')
        ->get();
    $buyerList = [];
    foreach ($buyers as $buyer) {
        $documents = BuyerController::getAttachment($buyer->reference_code);
        // $buyerList[] = [$buyer,$documents['success']?$documents['data']:[]];
        $success = isset($documents['success']) ? $documents['success'] : false;//added for validation
        $buyerList[] = [$buyer, $success ? $documents['data'] : []];
    }
    // dd($buyerList);
    return Inertia::render('Dashboard', ['buyers' => $buyerList]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/change-password', function () {
    $user = session('change_password_data');
    if (!$user) {
        Session::flush();
        Auth::logout();   
        return redirect('/login')->withErrors(['message' => 'Session expired. Please log in again.']);
    }

    return Inertia::render('Auth/ChangePassword', [
        'user' => $user,
    ]);
});
Route::get('buyer/register', [BuyerController::class, 'edit'])->middleware(['auth', 'verified'])->name('buyer.edit');
Route::post('buyer/update', [BuyerController::class, 'buyerUpdate'])->name('buyer.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
  
});

Route::post('voucher/generate', [VoucherController::class,'generateVoucher'])->middleware(['auth', 'verified'])->name('voucher.generate');
Route::resource('voucher', VoucherController::class)->middleware(['auth', 'verified'])->only(['create', 'store']);
Route::resource('projects', ProjectController::class);
Route::get('authenticate/login/{credential}', [AuthenticatedSessionController::class,'first_login']);
Route::resource('sync-projects', SyncProjectsController::class)->only(['create', 'store']);
require __DIR__.'/auth.php';
