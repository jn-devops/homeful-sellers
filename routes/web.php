<?php

use App\Http\Controllers\SyncProjectsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\DefaultSetupController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Homeful\Contacts\Contacts;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use Homeful\Contacts\Models\Customer as Contact;

Route::get('admin', function(){
    return Inertia::render('Admin/Admin');
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/dashboard', function () {
    $user = Auth::user();
    $statusList = DefaultSetupController::getStatus();
    // dd($statusList);
    $buyers = Contact::where('landline', $user->email . "/" . $user->meta->seller_commission_code)
        // ->whereNull('current_status')
        ->orderBy('created_at', 'desc')
        ->get();
        //  dd($buyers);
    $buyerList = [];
    $list_attachment = [];
    $pending_document =[];

    foreach ($buyers as $buyer) {
        $documents = BuyerController::getAttachment($buyer->reference_code);
        // dd($documents);
        if($documents['success']){
            // dd($documents['data']);
            $list_attachment[] = $documents['data']['list_attachment'];
            $pending_document =[];
        //    dd(($list_attachment));
            if(count($list_attachment)){
                foreach($list_attachment[0] as $attachment)
                {
                    if($attachment['exists']!==true){
                        // dd($attachment);
                        $pending_document[]=['code'=>$attachment['code'],'name'=>$attachment['name']];
                    }
                  //waiting for update sa document list 
                }

            }
           
        }
        $buyerList[] = [$buyer,$documents['success']?$documents['data']:[],$pending_document];
       
    }
    // dd($buyerList);
    return Inertia::render('Dashboard', ['buyers' => $buyerList, 'statusList' => $statusList]);
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
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');


Route::post('voucher/generate', [VoucherController::class,'generateVoucher'])->middleware(['auth', 'verified'])->name('voucher.generate');
Route::resource('voucher', VoucherController::class)->middleware(['auth', 'verified'])->only(['create', 'store']);
Route::resource('projects', ProjectController::class);
Route::get('authenticate/login/{credential}', [AuthenticatedSessionController::class,'first_login']);
Route::resource('sync-projects', SyncProjectsController::class)->only(['create', 'store']);
require __DIR__.'/auth.php';
