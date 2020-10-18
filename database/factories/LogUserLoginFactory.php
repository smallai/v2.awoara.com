<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\LogUserLogin::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'login_at' => $faker->dateTimeBetween('-30 days'),
        'ip' => $faker->ipv4,
        'src' => 'web',
        'remark' => $faker->word,
    ];
});
