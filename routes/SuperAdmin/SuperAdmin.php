<?php
use App\Http\Controllers\SUPER\SuperAdminController;

Route::group(['prefix' => 'superadmin', 'middleware' => 'auth'], function () {
    Route::get('/', [SuperAdminController::class, 'index'])->name('superadmin.index');
});