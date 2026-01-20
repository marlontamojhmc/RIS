<?php
use Inertia\Inertia;
use App\Models\Accreditation\Accreditation;
use App\Models\Locator\ApplicationModel;

Route::middleware(['auth'])
    ->prefix('applications')
    ->name('applications.')
    ->group(function () {

        Route::get('/permits', function(){
            return Inertia::render('Permiit/GatePass',[]);
        })->name('app.index');
        Route::get('/attach', function(){
           $accreditation = ApplicationModel::with('uploads')
                            ->where('form_number', 'CEO-0002')
                            ->get();
            dd($accreditation);
        });
});