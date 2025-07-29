<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\RedeemController;
use App\Http\Controllers\VoucherControllerv2 as VoucherController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ContactController;
// use App\Http\Controllers\VoucherController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EncryptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('voucher/generate', [VoucherController::class,'generateVoucher'])->name('api.voucher.generate');
// Route::post('redeem/{voucher}/{project?}', RedeemController::class)->name('redeem');
Route::get('redeem/{voucher}', [RedeemController::class,'validated_voucher'])->name('validated_voucher');
Route::post('redeem/{voucher}', [RedeemController::class,'redeem'])->name('redeem');

Route::post('buyer/update', [BuyerController::class, 'updateBuyer'])->name('api.buyer.update');
Route::get('buyer/sync/{referenceCode}', [BuyerController::class, 'syncBuyer'])->name('api.sync.update');
Route::post('buyer/sync/{referenceCode}', [BuyerController::class, 'syncBuyer'])->name('api.sync');

Route::post('notification/send-email', [NotificationController::class, 'send_email'])->name('api.sendEmail');
Route::post('notification/send-sms', [NotificationController::class, 'send_sms'])->name('api.sendSMS');

Route::delete('delete/contact-via-mobile/{mobile}', [ContactController::class, 'delete_via_mobile'])->name('api.delete-contact-via-mobile');
Route::post('buyer/match-api', [BuyerController::class, 'match'])->name('api.buyer.match');

Route::post('/encrypt',function(Request $request){
    return EncryptController::encrypt($request->input('credential'));
});
Route::post('/decrypt',function(Request $request){
    return EncryptController::decrypt($request->input('credential'));
});