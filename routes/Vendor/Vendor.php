<?php

use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Accreditation\AccreditationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {

        Route::get('/', [VendorController::class, 'index'])
            ->name('dashboard');

        Route::get('/accredit', [VendorController::class, 'accreditation'])
            ->name('accredit');

        Route::get('/accreditations/{id}', 
            [AccreditationController::class, 'show']
        )->name('accreditations.show');
    });
