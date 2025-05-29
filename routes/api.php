<?php

use App\Http\Controllers\RedeemController;
use App\Http\Controllers\VoucherControllerv2 as VoucherController;
use App\Http\Controllers\BuyerController;
// use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('voucher/generate', [VoucherController::class,'generateVoucher'])->name('api.voucher.generate');
Route::post('redeem/{voucher}/{project?}', RedeemController::class)->middleware(['auth', 'verified'])->name('redeem');
Route::post('buyer/update', [BuyerController::class, 'updateBuyer'])->name('api.buyer.update');
