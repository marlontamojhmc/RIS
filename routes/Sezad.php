<?php
use App\Http\Controllers\OSAC\OsacController;
use App\Http\Controllers\SEZAD\SezadManagerController;
use App\Http\Controllers\CCO\CcoController;
use App\Http\Controllers\FINANCE\FinanceController;

     Route::prefix('sezad')->group(function () {
        
        //CCO/CCA
        Route::get('/cco', [CcoController::class, 'index'])->name('cco.index');
        Route::get('/cco/{id}/show', [CcoController::class, 'show'])->name('cco.show');
        //Osac Processor
        Route::get('/osac', [OsacController::class, 'index'])->name('osac.index');
        Route::get('/apply', [OsacController::class, 'create'])->name('osac.create');
        Route::get('/osac/{id}/show', [OsacController::class, 'show'])->name('osac.show');
        //Sezad Manager
         Route::get('/manager', [SezadManagerController::class, 'index'])->name('sezad.manager.index');
         Route::get('/manager/{id}/show', [SezadManagerController::class, 'show'])->name('sezad.manager.show');
     })->name('sezad');
   
      //FSD
      Route::prefix('fsd')->group(function(){
            //FINANCE
        Route::get('/finance',[FinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/{id}/show', [FinanceController::class, 'show'])->name('finance.show');
      })->name('fsd');