<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Device::class, function (Faker $faker) {
    return [
        'product_key' => 'JX1Dg3BjVlv',
        'device_name' => 'test-20180326007',
        'device_secret' => 'Al5LOIy7jUyf9nbCnH9qZwJIrNgw96lG',
        'owner_id' => 1,
        'name' => $faker->word,
        'address' => $faker->address,
        'platform_fee_rate' => mt_rand(1, 100),
        'status' => \App\Models\Device::DeviceStatus_Online,
        'is_self' => false,
        'settings' => '{}',
    ];
});
