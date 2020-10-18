<?php

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

Route::get('/', 'WebController@index');
Route::get('/ua', 'WebController@showUserAgent');


////////////////////////////////////////////////////////////////////////////////////////
///     用户登录相关路由
////////////////////////////////////////////////////////////////////////////////////////
//|        | GET|HEAD  | login                                 | login                     | App\Http\Controllers\Auth\LoginController@showLoginForm                | web,guest                                    |
//|        | POST      | login                                 |                           | App\Http\Controllers\Auth\LoginController@login                        | web,guest                                    |
//|        | POST      | logout                                | logout                    | App\Http\Controllers\Auth\LoginController@logout                       | web
Route::get('login', 'Admin\LoginController@index')->name('login');
Route::post('login', 'Admin\LoginController@login');
Route::post('logout', 'Admin\LoginController@logout')->name('logout');
Route::post('register', 'Admin\LoginController@register')->name('register');


////////////////////////////////////////////////////////////////////////////////////////
///     设备扫码的入口地址
////////////////////////////////////////////////////////////////////////////////////////
Route::get('pay', 'RedirectUserController@entry')->name('redirect.user');
Route::any('code', 'Admin\DeviceQrCodeController@show')->name('device.qrcode');

//使用教程，泡沫和清水切换示意图
Route::get('tutorial', 'TutorialController@index')->name('tutorial');

////////////////////////////////////////////////////////////////////////////////////////
///     支付宝相关路由
////////////////////////////////////////////////////////////////////////////////////////
//支付宝显示商品信息
//支付宝用户的错误提示
Route::get('alipay/error', 'Alipay\VipCardController@error')->name('alipay.error');
//支付宝用户相关接口 , 'alipay.oauth'
Route::group(['prefix' => 'alipay', 'middleware' => ['web', 'alipay.oauth']], function () {
    Route::get('showGoods', 'Alipay\AlipayController@showGoods')->name('alipay.showGoods');

    //支付宝提交订单
    Route::any('order', 'Alipay\AlipayController@order')->name('alipay.order');
    //支付宝同步跳转   Alipay\AlipayController@returnUrl
    Route::get('returnUrl', 'Alipay\AlipayController@returnUrl')->name('alipay.returnUrl');

    //支付宝用户购买的会员卡
    Route::get('showCardInfo', 'Alipay\VipCardController@showCardInfo')->name('alipay.showCardInfo');
    //支付宝会员点击我要洗车
    Route::post('carWash', 'Alipay\VipCardController@carWash')->name('alipay.carWash');
    //会员卡开机成功
    Route::get('success', 'Alipay\VipCardController@success')->name('alipay.success');
});
//支付宝支付结果的异步通知
Route::get('alipay/notifyUrl/{orderId}', 'Alipay\AlipayController@notifyUrl')->name('alipay.notifyUrl');
//跳转到支付宝用户授权
Route::get('alipay/oauth', 'Alipay\AlipayOAuthController@redirectUser')->name('alipay.oauth');
//从支付宝用户授权返回，拿到用户资料
Route::get('alipay/oauth/callback/{key}', 'Alipay\AlipayOAuthController@callback')->name('alipay.callback');

//////////////////////////////////////////////////////////////////////////////////////
////     微信相关路由
//////////////////////////////////////////////////////////////////////////////////////
/// 公众号
Route::any('wechat/serve', 'WeChat\WeChatController@serve');

// 微信支付路由 'wechat.test',
Route::group(['prefix' => 'wechat', 'middleware' => ['web', 'wechat.oauth']], function () {
    //显示商品列表
    Route::get('showGoods', 'WeChat\WeChatPayController@showGoods')->name('wechat.showGoods');
    //下单
    Route::any('order', 'WeChat\WeChatPayController@order')->name('wechat.order');
    //支付完成
    Route::get('returnUrl', 'WeChat\WeChatPayController@returnUrl')->name('wechat.returnUrl');
    //显示会员卡信息
    Route::get('showCardInfo', 'WeChat\UserVipCardController@showCardInfo')->name('wechat.showCardInfo');
    //使用会员卡洗车
    Route::post('carWash', 'WeChat\UserVipCardController@carWash')->name('wechat.carWash');
    //使用会员卡开机成功
    Route::get('success', 'WeChat\UserVipCardController@success')->name('wechat.success');
    Route::get('error', 'WeChat\UserVipCardController@error')->name('wechat.error');
});

//微信支付通知
Route::any('wechat/notify/{orderId}', 'WeChat\WeChatPayController@notify')->name('wechat.notify');
Route::any('wechat/refund_notify', 'WeChat\WeChatPayController@refundNotify')->name('wechat.refund_notify');

