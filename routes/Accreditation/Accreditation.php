<?php
use App\Http\Controllers\Accreditation\AccreditationController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('accreditation')->group(function () {
    Route::get('/index', [AccreditationController::class,'index']);
    });
    Route::post('/accreditation/store', [AccreditationController::class, 'store'])
         ->name('accreditation.store');
});
Route::prefix('supplier')->group(function () {
    Route::get('/accreditation', [AccreditationController::class, 'SupplierAccreditation'])
        ->name('supplier.accreditation');
});
Route::prefix('commercial')->group(function () {
    Route::get('/accreditation', [AccreditationController::class, 'TradeFairAccreditation'])
        ->name('commercial.accreditation');
});