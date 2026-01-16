<?php
use App\Http\Controllers\SEZAD\RO\RegistrationOfficerController;

Route::prefix('sezad')
    ->name('sezad.ro.')
    ->group(function () {
        Route::get('/ro', [RegistrationOfficerController::class, 'index'])->name('index');
    });