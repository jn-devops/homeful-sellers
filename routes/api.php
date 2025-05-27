<?php

use App\Http\Controllers\RedeemController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('redeem/{voucher}/{project?}', RedeemController::class)->name('redeem');
Route::post('voucher/generate', [VoucherController::class, 'generateVoucher'])->name('voucher.generate');
