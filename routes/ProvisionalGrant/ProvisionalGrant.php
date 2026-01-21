<?php
use App\Http\Controllers\ProvisionalGrant\ProvisionalGrantController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('provisional')->group(function () {
        Route::post('/store', [ProvisionalGrantController::class,'store']);
        Route::get('/all', [ProvisionalGrantController::class,'all']);
    });
    
    
});