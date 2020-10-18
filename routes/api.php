<?php

Route::post('/send_verification_code', 'Api\SendVerificationCodeController@send')->name('send_verification_code');

//use Illuminate\Http\Request;

//Route::get('/send_code', 'Api/');

//Route::get('/send_code', 'Api\Send@index')->name('log.user.login');

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::middleware(['throttle'])->group(function () {
//Route::prefix('v1')->group(function () {
    //'role:super-admin|admin'
    //'permission:publish articles|edit articles'
//    Route::middleware(['throttle', 'auth:api' ])->group(function () {
//        Route::resource('user', 'Api\UserController');
//        Route::resource('device', 'Api\DeviceController');
//        Route::resource('goods', 'Api\GoodsController');
//        Route::resource('trade', 'Api\TradesController');
//        Route::resource('washcar', 'Api\WashCarController');
//        Route::resource('withdraw', 'Api\WithdrawController');
//        Route::resource('log_login', 'Api\LogUserLoginController');
//    });
//});