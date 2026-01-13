<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AblyController;
use App\Http\Controllers\NotificationAbly\NotificationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', function () {
    return ['status' => 'API working'];
});

Route::post('/ably/publish', [AblyController::class, 'publish']);


// route to get token request for client-side auth
Route::get('/ably/token-request', [AblyController::class, 'tokenRequest']);
Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');