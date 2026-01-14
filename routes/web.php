<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Auth\TemporaryAuthController;
use App\Helpers\UserHelper;
use App\Models\UserDetails;
use App\Http\Controllers\Users\UserDetailsController;
use App\Http\Controllers\Utilities\LookupController;
use App\Http\Controllers\BDD\BddController;

use App\Http\Controllers\SEZAD\SEZADController;
use App\Http\Controllers\Auth\GoogleOAuthController;
use App\Mail\ChangePasswordMail;
use App\Services\GmailService;
use App\Http\Controllers\SEZAD\Clearances\BringInController;
use App\Http\Controllers\SEZAD\Accreditation\AccreditationController;

use App\Http\Controllers\SEZAD\Accreditation\ServiceProviderSupplierController;
use App\Http\Controllers\Signup\SignupController;
use App\Http\Controllers\Signup\BusinessTypeController;
use App\Http\Controllers\Signup\LocatorController;
use App\Http\Controllers\Signup\TemporaryUserController;
use App\Http\Controllers\VENDOR\LocatorVendorController;
use App\Http\Controllers\TestPdfController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Keep all URL names intact. Organized by section for clarity.
|
*/

// 🔹 Public Route
Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

// 🔹 Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    //BDD
    Route::prefix('bdd')->group(function (){
        Route::get('/dash', [BDDController::class, 'index'])->name('bdd.Dashboard');
        Route::get('/mylocators',[BDDController::class, 'locators'])->name('bdd.myLocators');
        Route::post('/locator/saveProfile', [BDDController::class, 'saveLocatorProfile'])->name('locator.save');
    });
    
    // Dashboard
    Route::get('dashboard', function () {
        $user = UserHelper::loadUserWithDetails();
        return Inertia::render('Dashboard', ['auth' => ['user' => $user]]);
    })->name('dashboard');

    Route::prefix('users')->group(function () {
            Route::get('/', [UserDetailsController::class, 'index'])->name('usersDashboard');
            Route::get('/list', [UserDetailsController::class, 'index'])->name('users.list');
            Route::post('/addUser', [UserDetailsController::class, 'store'])->name('userDetails.store');
        });
   

    /*
    |--------------------------------------------------------------------------
    | SEZAD
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'verified', 'role.access'])->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        
        /*
        |-------------------------------------------------------------------------
        | BDD
        |--------------------------------------------------------------------------
        */
        
        Route::prefix('sezad')->group(function () {
            Route::get('/', [SEZADController::class, 'index'])->name('sezadDashboard');

            Route::prefix('clearances')->group(function () {
                Route::get('/bring-in', [BringInController::class, 'index'])->name('bringInclearance');
            });
            Route::prefix('accreditation')->group(function () {
                Route::get('/', [AccreditationController::class, 'index'])->name('accreditation');
                //Route::get('/new', [AccreditationNewController::class, 'index'])->name('accreditationNew');
                //Route::get('/renewal', [AccreditationRenewalController::class, 'index'])->name('accreditationRenewal');
                //Route::get('/provisional', [AccreditationProvisionalController::class, 'index'])->name('accreditationProvisional');
                Route::get('service-provider', [ServiceProviderSupplierController::class, 'index'])->name('serviceProviderSupplier');
                Route::prefix('service-provider')->group(function () {
                    Route::get('/new', [ServiceProviderSupplierController::class, 'index'])->name('serviceProvidernNew');
                    Route::get('/renewal', [ServiceProviderSupplierController::class, 'index'])->name('serviceProviderRenewal');
                });
                Route::get('event-operator', [ServiceProviderSupplierController::class, 'index'])->name('eventOperator');
                Route::prefix('vendor')->group(function () {
                    Route::get('/new', [ServiceProviderSupplierController::class, 'index'])->name('vendorNew');
                    Route::get('/renewal', [ServiceProviderSupplierController::class, 'index'])->name('vendorRenewal');
                });
                Route::get('provitional', [ServiceProviderSupplierController::class, 'index'])->name('provitional');

                Route::get('/temporary-users', [TemporaryUserController::class, 'index']);
                Route::post('/temporary-users', [TemporaryUserController::class, 'store']);
                Route::delete('/temporary-users/{id}', [TemporaryUserController::class, 'destroy']);
            });
            Route::post('/temp-users/update', [SEZADController::class, 'updateTempUser']);
           
        });
    });
    /*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    */
    Route::get('address', fn() => Inertia::render('users/Address'))->name('usersAddress');

    /*
    |--------------------------------------------------------------------------
    | Utilities
    |--------------------------------------------------------------------------
    */
    Route::get('/departments', [LookupController::class, 'departments'])->name('departments');
    Route::get('/divisions', [LookupController::class, 'divisions'])->name('divisions');
    Route::get('/roles', [LookupController::class, 'roles'])->name('roles');
    Route::get('/permissions', [LookupController::class, 'permissions'])->name('permissions');

    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    */
    Route::get('/sendChangePassword', [UserDetailsController::class, 'sendChangePassword'])->name('sendChangePassword');
});
//sign up
Route::get('/signup', [SignupController::class, 'index'])->name('signup');
Route::post('/signupSave', [SignupController::class, 'store'])->name('signupSave');
Route::get('/business-types', [BusinessTypeController::class, 'index']);
Route::get('/business-types/{id}/categories', [BusinessTypeController::class, 'categories'])->name('getBusinessCategories');
Route::get('/locatorsSignUp', [LocatorController::class, 'index'])->name('locatorsSignUp');



Route::prefix('temp')->name('temp.')->group(function () {

    // GET /temp/login
    Route::get('/login', function () {
        return inertia('auth/TempLogin');
    })->name('login');

    // POST /temp/login
    Route::post('/login', [TemporaryAuthController::class, 'login'])
        ->name('login.submit');

    // POST /temp/logout
    Route::post('/logout', [TemporaryAuthController::class, 'logout'])
        ->name('logout');
    Route::get('/dashboard', function () {
        return inertia('auth/TempDash');
    });
});
//VENDOR
Route::get('/locator-vendor',[LocatorVendorController::class,'index'])->name('locator.vendor.index');

Route::get('/422', function(){
   abort(422);
});


Route::get('/test-pdf', [TestPdfController::class, 'index']);
Route::get('/pdf/{id}/generate', [TestPdfController::class, 'generate'])
    ->name('pdf.generate');



// 🔹 Include other route files
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/locator/locator.php';
require __DIR__ . '/locator/notification.php';
require __DIR__ .'/Sezad.php';
require __DIR__ .'/ServiceProvider/SP.php';
require __DIR__ .'/Vendor/Vendor.php';
require __DIR__ .'/Accreditation/Accreditation.php';
