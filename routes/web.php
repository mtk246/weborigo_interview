<?php

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
    return view('welcome');
});

//// API routes
Route::get('/device/info/{id}', 'RegisterDeviceController@getDevice')->middleware(['xApiKey']);
Route::post('/api/device/register', 'RegisterDeviceController@postDevice');

Route::get('/leasing/update/{id}', 'DeviceLeasingController@getDeviceLeasing')->middleware(['xApiKey']);
Route::post('/api/device/device_leasing', 'DeviceLeasingController@postDeviceLeasing')->middleware(['xApiKey']);

//// API Routes End
