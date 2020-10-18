<?php

return [
    'access_key_id' => env('ALIYUN_IOT_ACCESS_KEY_ID'),
    'access_secret' => env('ALIYUN_IOT_ACCESS_SECRET'),
    'endpoint' => env('ALIYUN_IOT_MNS_ENDPOINT'),
    'queue_name' => env('ALIYUN_IOT_MNS_QUEUE_NAME'),
    'product_key' => env('ALIYUN_IOT_PRODUCT_KEY'),
    'wait_seconds' => env('ALIYUN_IOT_MNS_WAIT_SECONDS', 30),
];
