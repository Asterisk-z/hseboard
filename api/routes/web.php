<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return response()->json(['true'], 200);
});

Route::any('/no-feelings', function ($router) {
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('migrate');
    Artisan::call('db:seed');
    return response()->json([], 200);
});

Route::any('/no-feelings-all', function ($router) {
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
    return response()->json([], 200);
});
