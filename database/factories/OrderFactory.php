<?php

use Faker\Generator as Faker;

$factory->define(\App\Order::class, function (Faker $faker) {
    return [
//        'id'
        'user_id' => function() {
//            return factory(\App\User::class)->create()->id;
            return null;
        },
        'owner_id' => function() {
//            return factory(\App\User::class)->create()->id;
            return null;
        },
        'device_id' => function() {
//            return factory(\App\Device::class)->create()->id;
            return null;
        },
//        'trade_id' => function() {
////            return factory(\App\Trade::class)->create()->id;
//            return null;
//        },
//        'log_user_login_id' => null,
        'user_ip' => null,
        'user_phone' => null,
        'user_email' => null,
        'confirm_status' => mt_rand(0, 1),
        'confirmed_at' => $faker->dateTimeBetween('-30 days'),
    ];
});
