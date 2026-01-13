<?php
use App\Http\Controllers\Locator\LocatorController;
use App\Http\Controllers\Applications\ApplicationsController;
use App\Http\Controllers\Locator\ArticleDetailController;
use App\Http\Controllers\Locator\UploadController;
use App\Http\Controllers\Locator\ApplicationForApprovalController;
use App\Helpers\AppConstants;
use App\Models\Locator\ApplicationModel;
use App\Models\User;
use App\Http\Controllers\Applications\ATOController;
use App\Data\ATOmeta;
use Carbon\Carbon;
use App\Models\ATO\AtoApplication;
use App\Http\Controllers\SERVICEPROVIDER\LocatorServiceProviderController;
//ServiceProvider
Route::get('/SP', [LocatorServiceProviderController::class,'index'])->name('locator.serviceProvider.index');
Route::post('/locator/serviceProviderRequest/{id}/approve',[LocatorController::class, 'approveServiceProviderRequest'])->name('locator.serviceProvider.approve');
Route::get('/MyServiceProviders',[LocatorController::class, 'MyServiceProviders'])->name('locator.MyServiceProviders');
Route::get('/serviceProviderRequest', [LocatorController::class,'serviceProviderRequest'])->name('locator.serviProvider');
Route:: get('/MyVendors',[LocatorController::class, 'myVendors'])->name('locator.myvendor');
Route::get('/VendorVerify',[LocatorController::class, 'vendorRequest'])->name('locator.vendor.request');
Route::post('/locator/vendor/{id}/approve', [LocatorController::class, 'approveVendorRequest'])->name('locator.vendor.approve');
Route::get('/locator', [LocatorController::class, 'index'])->name('locator');
Route:: get('/locator/{id}', [LocatorController::class, 'show'])->name('locator.app.show');
Route::resource('approval', ApplicationForApprovalController::class);
Route::group(['prefix' => 'loctr', 'middleware' => 'auth'], function () {
    //route for pending view
    Route::get('applications/pending', [ApplicationsController::class, 'pendingList'])->name('applications.pending');
    //Route for single pending view
    Route::get('applications/{id}/pending',[LocatorController::class, 'pendingShow'])->name('applications.pendingShow');
    //route for approved 
    Route::get('applications/{id}/approved', [LocatorController::class, 'approvedShow'])->name('applications.approvedShow');
    Route::get('applications/approved', [ApplicationsController::class, 'approvedList'])->name('applications.approved');
    //route for creating application (crud) 
    Route::resource('applications', ApplicationsController::class);
    //route articles(crud) 
    Route::resource('articles', \App\Http\Controllers\Locator\ArticleDetailController::class);
    Route::post('/articles/{id}/verify', [ArticleDetailController::class, 'verifyArticle'])
    ->name('articles.verify');
    //route for uploads/attachment (crud) 
    Route::resource('uploads', UploadController::class);
    //route for declared vue and validity 
    Route::post('/uploads/{id}/verify', [UploadController::class, 'verify']);
    Route::post('/applications/option-selection', [\App\Http\Controllers\Locator\ApplicationController::class, 'saveOptionSelection'])
    ->name('applications.option-selection');
});

//routes for application approve and return
//approve
Route::post('application-for-approval/{form_number}/approvers/{approverId}/approve', [ApplicationForApprovalController::class, 'approve'])
    ->name('application-for-approval.approve');
//for invoice
Route::post('application-for-approval/{form_number}/approvers/{approverId}/invoice',[ApplicationForApprovalController::class, 'invoice'])
   ->name('application-for-approval.invoice');
    //Return
Route::post('application-for-approval/{id}/approvers/{approverId}/return', [ApplicationForApprovalController::class, 'returnApproval'])
    ->name('application-for-approval.return');
//Reject
Route::post('application-for-approval/{id}/approvers/{approverId}/reject', [ApplicationForApprovalController::class, 'reject'])
    ->name('application-for-approval.reject');
//how form meta works
Route::get('/test-meta', function(){
   $applicationTest = App\Models\Locator\ApplicationModel::find(15);
   $applicationTest->setMeta('processing', 'processing required.');
    $meta= $applicationTest->getMeta('processing');
dd($meta);

});
Route::resource('ATO',ATOController::class);
Route::get('/ato/viewer', [ATOController::class, 'MyAto'])->name('My.Ato');
// Route::get('/app-test', function(){
//     $app = ApplicationModel::where('id',61)->first();
    
// $details = new ATOmeta(
//     application_date: "11/17/2025",
//     application_type: "Renewal",
//     corporate_name: "",
//     file_uploaded: "image/1.png",
//     office_address: "johnhay AYALA",
//     owner_email: "sample@gmail.com",
//     owner_mobile: "09950850882",
//     owner_name: "Merlita Tamo",
//     representative_email: "Example@gmail.com",
//     representative_mobile: "123456789",
//     trade_name: "YumYam",
// );

// // SAVE as array
// $app->setMeta('AppMeta2', $details->toArray());

// // RETRIEVE
// $meta = $app->getMeta('AppMeta2');  // array
// //after retrieve create object from Array
// $metaObject = ATOmeta::fromArray($meta);
// dd($metaObject);
// $date = Carbon::createFromFormat('m/d/Y', $metaObject->application_date);

// $expirationDate = $date->copy()->endOfYear();

// //dd('ATO is Valid until: '.$expirationDate->format('F j, Y'));
//  });
 
// routes/web.php
// Route::middleware(['block'])->group(function () {
//     Route::get('/test-block', function () {
//         return 'This will never load';
//     });
// });

