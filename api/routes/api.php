<?php

use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('validate/account', [AuthController::class, 'validateAccount']);

    Route::prefix('reset')->group(function () {
        Route::post('/initiate', [AuthController::class, 'forgotPassword']);
        Route::post('/otp', [AuthController::class, 'validateForgotPasswordOtp'])->middleware('throttle:10,5');
        Route::post('/complete', [AuthController::class, 'resetPassword'])->middleware('passwordReset');
    });

});

Route::get('account-types', [AccountTypeController::class, 'index']);
Route::get('countries', [CountryController::class, 'index']);
Route::get('industries', [IndustryController::class, 'index']);

Route::middleware('auth:api')->group(function ($router) {

    Route::prefix('auth')->group(function ($router) {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::prefix('offers')->group(function ($router) {
        Route::get('/', [OfferController::class, 'index']);
        Route::post('send', [OfferController::class, 'store']);
        Route::post('action-offer', [OfferController::class, 'action']);
    });

    Route::prefix('organizations')->group(function ($router) {
        Route::get('/', [OrganisationController::class, 'index']);
        Route::get('/{organization_id}', [OrganisationController::class, 'show']);
        Route::post('/remove-user', [OrganisationController::class, 'removeUser']);
    });

    Route::prefix('users')->group(function ($router) {
        Route::get('/{organization_id}', [UsersController::class, 'index']);
        Route::get('/{organization_id}/{user_id}', [UsersController::class, 'show']);
    });

});

Route::any('/{any}', function ($router) {
    return response()->json([
        'status' => '404 Not Found',
        'statusCode' => '404',
    ], 404);
});
