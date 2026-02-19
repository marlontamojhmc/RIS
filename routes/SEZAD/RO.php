<?php
use App\Http\Controllers\SEZAD\RO\RegistrationOfficerController;

Route::prefix('sezad')
    ->name('ro')
    ->group(function () {
        Route::get('/ro', [RegistrationOfficerController::class, 'index'])->name('sezad.ro.index');
    });