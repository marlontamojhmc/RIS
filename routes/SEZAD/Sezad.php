<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OSAC\OsacController;
use App\Http\Controllers\SEZAD\SezadManagerController;
use App\Http\Controllers\CCO\CcoController;
use App\Http\Controllers\FINANCE\FinanceController;
use App\Http\Controllers\SEZAD\RO\RegistrationOfficerController;
use App\Http\Controllers\SEZAD\SEZADController;

     Route::prefix('sezad')->group(function () {
      // access index
      Route::get('/', [SezadManagerController::class, 'index'])->name('sezad.index');
      // approve per signatory
      Route::post('/approve', [SEZADController::class, 'updateStatus'])->name('signatory.approve');
      // approve/payment for finance signatory
      Route::post('/accept-payment', [SEZADController::class, 'payment'])->name('sezad');
      //CCO/CCA
        Route::get('/cco/{id}/show', [CcoController::class, 'show'])->name('cco.show');
        //Osac Processor
        Route::post('/store2',[OsacController::class,'store2'])->name('osac.store2');
        Route::get('/index2',[OsacController::class,'Index2'])->name('osac.index2');
        Route::get('/osac', [OsacController::class, 'index'])->name('osac.index');
        Route::get('/apply', [OsacController::class, 'create'])->name('osac.create');
        Route::get('/osac/{id}/show', [OsacController::class, 'show'])->name('osac.show');
        Route::post('/osac/approve',[OsacController::class,'Approve'])->name('osac.approve');
        Route::post('/osac/return',[OsacController::class, 'Return'])->name('osac.return');
        Route::get('/osac/accreditations',[OsacController::class,'AccreditationPage'])->name('osac.accreditation');
        Route::get('/osac/permits',[OsacController::class,'PermitsPage'])->name('osac.permits');
        Route::get('/osac/Provisional',[OsacController::class,'ProvisionalGrantPage'])->name('osac.provisionalGrant');
     }); //Sezad Manager
         // Route::get('/manager', [SezadManagerController::class, 'index'])->name('sezad.manager.index');

        
        //FSD
        Route::prefix('fsd')->group(function(){
         Route::post('/accept-payment', [FinanceController::class, 'payment'])->name('finance.payment');
      });