<?php
use App\Http\Controllers\VENDOR\VendorController;
Route::get('/vendor', [VendorController::class,'index']);
Route::get('accredit/vendor', [VendorController::class, 'accreditation']);