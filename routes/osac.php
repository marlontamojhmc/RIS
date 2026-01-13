<?php
use App\Http\Controllers\OSAC\OsacController;
use App\Http\Controllers\Applications\ATOController;
Route::prefix('sezad')->group(function () {

    Route::get('/osac', [OsacController::class, 'index'])->name('osac.index');

    Route::get('/apply', [OsacController::class, 'create'])->name('osac.create');

    Route::get('/{id}/show', [OsacController::class, 'show'])->name('osac.show');

})->name('osac');
Route::get('/create2', [ATOController::class, 'CreateApp'])->name('app.create2');
