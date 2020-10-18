<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\WithdrawMoney::class, function (Faker $faker) {
    return [
        'owner_id' => function () {
            return factory(\App\Models\User::class)->create()->id;
        },
        'status' => function () {
            $items = array(
                \App\Models\Trade::WithdrawStatus_None,
                \App\Models\Trade::WithdrawStatus_Request,
                \App\Models\Trade::WithdrawStatus_Confirmed,
                \App\Models\Trade::WithdrawStatus_Processing,
                \App\Models\Trade::WithdrawStatus_Success,
                \App\Models\Trade::WithdrawStatus_Failed,
            );
            $idx = mt_rand(0, count($items)-1);
            return $items[ $idx ];
        },
        'payee' => $faker->word,
        'real_name' => $faker->name,
        'payment_money' => mt_rand(10, 100),
        'refund_money' => mt_rand(10, 100),
        'withdraw_money' => mt_rand(10, 100),
        'platform_money' => mt_rand(10, 100),
    ];
});
