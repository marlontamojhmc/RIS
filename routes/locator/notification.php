<?php 
use App\Http\Controllers\NotificationController;
use App\Models\User;
use App\Notifications\SezrisNotification;
use App\Models\ApplicationCategoryOption;
use Illuminate\Support\Facades\Route;
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
Route::get('/test-notification', function () {
    $user = User::first(); // pick a user, or use auth()->user()

    $user->notify(new SezrisNotification());

    return redirect()->back()
        ->with('success', 'Notification sent!');
});

