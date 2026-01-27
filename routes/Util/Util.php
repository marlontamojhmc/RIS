<?php
use App\Http\Controllers\Util\UtilController;

Route::prefix('util')
    ->name('pages.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/form', [UtilController::class, 'Form'])->name('form');
        Route::get('/holidays', [UtilController::class, 'Holiday'])->name('holidays.index');
    });