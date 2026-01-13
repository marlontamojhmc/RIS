<?php
use App\Http\Controllers\VENDOR\VendorController;
Route::get('/vendor', [VendorController::class,'index']);