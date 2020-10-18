<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Goods::class, function (Faker $faker) {
    return [
        'device_id' => 1,
        'owner_id' => 1,
        'name' => $faker->streetAddress,
        'price' => mt_rand(1, 1000),
//        'image' => $faker->imageUrl(),
        'is_sale' => true,
        'is_recommend' => $faker->boolean,
        'count' => mt_rand(1, 2),
        'seconds' => 60,
        'days' => 1,
    ];
});
