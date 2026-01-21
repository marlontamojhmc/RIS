<?php
use Inertia\Inertia;
use App\Models\Accreditation\Accreditation;
use App\Models\Locator\ApplicationModel;

Route::middleware(['auth'])
    ->prefix('applications')
    ->name('applications.')
    ->group(function () {
        Route::get('/accredit/{id}', function ($id) {
            $application = ApplicationModel::with('accreditation','uploads')
                ->where('id', $id)
                ->firstOrFail();

            dd($application);
        });
        Route::get('/permits', function(){
            return Inertia::render('Permiit/GatePass',[]);
        })->name('app.index');
        Route::get('/attach', function(){
           $accreditation = ApplicationModel::with('uploads')
                            ->where('form_number', 'PG-0001')
                            ->get();
            dd($accreditation);
        });
});