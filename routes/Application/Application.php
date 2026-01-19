<?php
use Inertia\Inertia;

Route::middleware(['auth'])
    ->prefix('applications')
    ->name('applications.')
    ->group(function () {

        Route::get('/permits', function(){
            return Inertia::render('Permiit/GatePass',[]);
        })->name('app.index');
});