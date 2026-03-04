<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OSAC\OsacController;
use App\Http\Controllers\SEZAD\SezadManagerController;
use App\Http\Controllers\CCO\CcoController;
use App\Http\Controllers\FINANCE\FinanceController;
use App\Http\Controllers\SEZAD\RO\RegistrationOfficerController;
use App\Http\Controllers\SEZAD\SEZADController;

    Route::prefix('sezad')->middleware(['auth','verified'])->name('sezad.')->group(function () {
    
    // SEZAD main dashboard
    Route::get('/', [SEZADController::class, 'index'])->name('index'); // -> 'sezad.index'
    
    // Approve per signatory
    Route::post('/approve', [SEZADController::class, 'updateStatus'])->name('signatory.approve'); // -> 'sezad.signatory.approve'
    
    // Approve/payment for finance
    Route::post('/accept-payment', [SEZADController::class, 'payment'])->name('payment'); // -> 'sezad.payment'

    // CCO/CCA
    Route::get('/cco/{id}/show', [CcoController::class, 'show'])->name('cco.show'); // -> 'sezad.cco.show'

    // OSAC Processor
    Route::prefix('osac')->group(function () {
        Route::get('/', [OsacController::class, 'index'])->name('osac.index'); // -> 'sezad.osac.index'
        Route::get('/apply', [OsacController::class, 'create'])->name('osac.create'); // -> 'sezad.osac.create'
        Route::get('/{id}/show', [OsacController::class, 'show'])->name('osac.show'); // -> 'sezad.osac.show'
        Route::post('/store2', [OsacController::class,'store2'])->name('osac.store2'); // -> 'sezad.osac.store2'
        Route::post('/approve', [OsacController::class,'Approve'])->name('osac.approve'); // -> 'sezad.osac.approve'
        Route::post('/return', [OsacController::class,'Return'])->name('osac.return'); // -> 'sezad.osac.return'

        // Special pages
        Route::get('/accreditations', [OsacController::class,'AccreditationPage'])->name('osac.accreditation'); // -> 'sezad.osac.accreditation'
        Route::get('/permits', [OsacController::class,'PermitsPage'])->name('osac.permits'); // -> 'sezad.osac.permits'
        Route::get('/provisional', [OsacController::class,'ProvisionalGrantPage'])->name('osac.provisionalGrant'); // -> 'sezad.osac.provisionalGrant'
    });
}); //Sezad Manager
         // Route::get('/manager', [SezadManagerController::class, 'index'])->name('sezad.manager.index');

        
        //FSD
      //   Route::prefix('fsd')->group(function(){
      //    Route::post('/accept-payment', [FinanceController::class, 'payment'])->name('finance.payment');
      // });