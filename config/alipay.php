<?php

$app_url = env('APP_URL');

return [
    'charset' => 'utf-8',

    //是否使用沙箱模式
    'mode' => env('ALIPAY_MODE', 'normal'),

    //蚂蚁金服开放平台应用ID
    'app_id' => env('ALIPAY_APP_ID', 0),

    //蚂蚁金服开放平台商户ID
    'seller_id' => env('ALIPAY_SELLER_ID', 0),

    //商户私钥
    'private_key' => env('ALIPAY_PRIVATE_KEY', 'see config/alipay.php'),

    //支付宝公钥
    'ali_public_key' => env('ALIPAY_PUBLIC_KEY', 'see config/alipay.php'),

    //付款成功后的同步跳转地址
    'return_url' => env('ALIPAY_RETURN_URL', $app_url . '/alipay/returnUrl'),

    //付款成功后的通知地址
    'notify_url' => env('ALIPAY_NOTIFY_URL', $app_url . "/alipay/notifyUrl"),

    //用户授权回调地址
    'redirect_url' => env('ALIPAY_REDIRECT_URL', $app_url . "/alipay/oauth"),

    'log' => [
        'file' => storage_path('logs/alipay_payment.log'),
        'level' => 'debug'
    ],
];
