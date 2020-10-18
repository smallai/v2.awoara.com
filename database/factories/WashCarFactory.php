<?php

use Faker\Generator as Faker;

$factory->define(\App\WashCar::class, function (Faker $faker) {
    return [
        'device_id' => function () {
            $count = \App\Models\Device::all()->count();
            return random_int(0, 100) % $count;
        },
//        'trade_id' => function () {
//            $count = \App\Models\Trade::all()->count();
//            return random_int(0, 100) % $count;
//        },
        'used_seconds' => mt_rand(1, 600),
        'total_seconds' => mt_rand(1, 600),
        'free_seconds' => mt_rand(1, 600),
        'water_seconds' => mt_rand(1, 600),
        'cleaner_seconds' => mt_rand(1, 600),
        'tap_switch_seconds' => mt_rand(1, 600),
        'water_count' => mt_rand(1, 600),
        'cleaner_count' => mt_rand(1, 600),
        'tap_switch_count' => mt_rand(1, 600),
        'temperature' => mt_rand(1000, 5000),
        'begin_at' => $faker->dateTimeBetween('-30 days'),
        'end_at' => $faker->dateTimeBetween('-30 days'),
        'created_at' => $faker->dateTimeBetween('-30 days'),
        'updated_at' => $faker->dateTimeBetween('-30 days'),
    ];
});
