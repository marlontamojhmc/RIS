<?php
use App\Http\Controllers\Accreditation\AccreditationController;

Route::middleware(['auth'])->group(function () {
    Route::post('/accreditation/store', [AccreditationController::class, 'store'])
         ->name('accreditation.store');
});