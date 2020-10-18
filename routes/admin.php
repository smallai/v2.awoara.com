<?php

use App\Models\User;
use Carbon\Carbon;

Route::get('test_db', function () {
//    Log::debug('db', \Illuminate\Support\Facades\DB::getConfig());
//    Log::debug('users', User::all()->toArray());
    return Carbon::now()->toString();
});

Route::middleware(['throttle:10,1', ])->group(function () {
    Route::get('login', 'Admin\LoginController@index')->name('admin.index');
    Route::post('login', 'Admin\LoginController@login')->name('admin.login');
    Route::get('logout', 'Admin\LoginController@logout')->name('admin.logout');
    Route::post('logout', 'Admin\LoginController@logout')->name('admin.logout');
});

Route::middleware(['auth.admin', 'throttle', ])->group(function () {
    Route::get('/', 'Admin\DashboardController@index')->name('dashboard.index');
    Route::resource('center', 'Admin\CenterController');
    Route::resource('device', 'Admin\DeviceController');
    Route::resource('user', 'Admin\UserController');
    Route::resource('goods', 'Admin\GoodsController');
    Route::resource('withdraw', 'Admin\WithdrawMoneyController');
    Route::resource('trade', 'Admin\TradeController');
    Route::resource('washcar', 'Admin\WashCarController');
    Route::resource('card', 'Admin\UserVipCardController')->only('index');
    Route::get('log/user_login/{user_id}', 'Admin\LogUserLoginController@index')->name('log.user.login');

//    Route::get('userDataTable', 'Admin\UserController@dataTableIndex')->name('user.dataTable');
//    Route::get('userDataTableData', 'Admin\UserController@dataTableData')->name('user.dataTableData');

    //显示设置管理员界面和修改管理员
    Route::get('_device/show_set_admin', 'Admin\DeviceController@showSetAdmin')->name('device.show_set_admin');
    Route::post('_device/set_admin', 'Admin\DeviceController@setAdmin')->name('device.set_admin');

    //显示退款界面和提交退款
    Route::get('_trade/refund', 'Admin\TradeController@showRefundMoney')->name('showRefundMoney');
    Route::post('_trade/refund', 'Admin\TradeController@refundMoney')->name('refundMoney');

    // 使用当前密码来修改密码
    Route::get('_user/reset_password', 'Admin\ResetPasswordController@show')->name('user.reset_password');
    Route::post('_user/reset_password', 'Admin\ResetPasswordController@update');

    //管理员强制重新设置密码
    Route::get('_user/force_reset_password', 'Admin\ForceResetPasswordController@show')->name('user.force_reset_password');
    Route::post('_user/force_reset_password', 'Admin\ForceResetPasswordController@update');

    //刷新设备状态
    Route::get('_device/refresh_state', 'Admin\RefreshDeviceStateController@update')->name('device.refresh_state');

    //把提现失败的记录删掉，并重置状态，允许用户重新提交提现请求。
    Route::post('_withdraw/reset', 'Admin\WithdrawMoneyController@reset')->name('withdraw.reset');
});

