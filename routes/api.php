<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DistributionController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\RecipientController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/public/recipients', [RecipientController::class, 'getAll']);
Route::get('/public/distributions', [DistributionController::class, 'getAll']);

Route::get('/public/announcement', [SettingController::class, 'getAnnouncement']);
Route::get('/public/contact', [SettingController::class, 'getContact']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/recipients', [RecipientController::class, 'show']);

    Route::get('logs/{userId}', [LogController::class, 'getLogsByUserId']);

    Route::get('/distributions', [DistributionController::class, 'show']);
    Route::post('/distributions', [DistributionController::class, 'create']);
    Route::get('/distribution/count', [DistributionController::class, 'countRecipientsByVillageId']);
});
