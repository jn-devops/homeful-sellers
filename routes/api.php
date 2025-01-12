<?php

use App\Http\Controllers\RedeemController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('redeem/{voucher}', RedeemController::class)->name('redeem');
