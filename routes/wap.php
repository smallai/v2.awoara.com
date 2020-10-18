<?php

Route::middleware(['throttle:60,1', ])->group(function () {
    Route::get('login', 'Wap\LoginController@showLoginForm')->name('wap.showLoginForm');
    Route::post('login', 'Wap\LoginController@login')->name('wap.login');
    Route::get('logout', 'Wap\LoginController@logout')->name('wap.logout');
    Route::post('logout', 'Wap\LoginController@logout')->name('wap.logout');
});