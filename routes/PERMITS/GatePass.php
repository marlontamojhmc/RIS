<?php
use App\Http\Controllers\PERMITS\GP\GatePassController;


Route::prefix('permits')->middleware(['auth'])->group(function () {
    Route::get('/gate-pass', [GatePassController::class, 'index'])->name('permits.gate-pass');
    Route::post('/gatepass/submit', [GatePassController::class, 'store'])->name('permits.gatepass.store');
});