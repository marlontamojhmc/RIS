<?php
use App\Http\Controllers\PERMITS\GP\GatePassController;
use App\Http\Controllers\PERMITS\BIC\BringInClearanceController;
use App\Http\Controllers\PERMITS\BOC\BringOutClearanceController;
use App\Http\Controllers\PERMITS\TBOC\TemporaryBringOutClearanceController;
use App\Http\Controllers\PERMITS\LPC\LocalPurchaseClearanceController;


Route::prefix('permits')->middleware(['auth'])->group(function () {
    Route::get('/gate-pass', [GatePassController::class, 'index'])->name('permits.gate-pass');
    Route::post('/gatepass/submit', [GatePassController::class, 'store'])->name('permits.gatepass.store');
    Route::post('/Bic/submit', [BringInClearanceController::class,'store'])->name('permits.bringInClearance.store');
    Route::post('/Boc/submit',[BringOutClearanceController::class,'store'])->name('permits.bringOutClearance.store');
    Route::post('/Tboc/submit',[TemporaryBringOutClearanceController::class,'store'])->name('permits.TemporaryBringOutClearance.store');
    Route::post('/Lpc/submit',[LocalPurchaseClearanceController::class, 'store'])->name('permits.LocalPurchaseClearance.store');
});