<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Trade::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\Models\User::class)->create()->id;
        },
        'device_id' => function () {
            return factory(\App\Models\Device::class)->create()->id;
        },
        'owner_id' => function () {
            return factory(\App\Models\Device::class)->create()->id;
        },

//        'goods_id' => function () {
//            return factory(\App\Models\Goods::class)->create()->id;
//        },
//        'goods_status' => function () {
//            $items = [
////                \App\Models\Trade::GoodsStatus_None,
//                \App\Models\Trade::GoodsStatus_Confirmed,
//            ];
//            $idx = mt_rand(0, count($items)-1);
//            return $items[ $idx ];
//        },
//        'goods_confirmed_at' => $faker->dateTimeBetween('-30 days'),
//
//        'payment_status' => function() {
//            $items = [
////                \App\Models\Trade::PaymentStatus_Processing,
//                \App\Models\Trade::PaymentStatus_Success,
////                \App\Models\Trade::PaymentStatus_Failed,
////                \App\Models\Trade::PaymentStatus_Failed,
//            ];
//            $idx = mt_rand(0, count($items)-1);
//            return $items[ $idx ];
//        },
//        'payment_type' => function () {
//            $items = array(
////                \App\Models\Trade::PaymentType_Coin,
////                \App\Models\Trade::PaymentType_Banknote,
////                \App\Models\Trade::PaymentType_Card,
//                \App\Models\Trade::PaymentType_Alipay,
////                \App\Models\Trade::PaymentType_WeChat,
////                \App\Models\Trade::PaymentType_VipCard,
//            );
//            $idx = mt_rand(0, count($items)-1);
//            return $items[ $idx ];
//        },
//        'payment_trade_id' => ''.$faker->randomNumber(8),
//        'payment_money' => mt_rand(1, 1000),
//        'payment_at' => $faker->dateTimeBetween('-7 days'),
//
//        'refund_status' => function () {
//            $items = array(
//                \App\Models\Trade::RefundStatus_None,
////                \App\Models\Trade::RefundStatus_Processing,
////                \App\Models\Trade::RefundStatus_Success,
////                \App\Models\Trade::RefundStatus_Failed,
//            );
//            $idx = mt_rand(0, count($items)-1);
//            return $items[ $idx ];
//        },
//        'refund_id' => ''.$faker->randomNumber(8),
//        'refund_money' => mt_rand(0, 1),
//        'refund_remark' => $faker->word(),
//        'refund_at' => $faker->dateTimeBetween('-30 days'),
//
//        'withdraw_status' => function () {
//            $items = array(
//                \App\Models\Trade::WithdrawStatus_None,
////                \App\Models\Trade::WithdrawStatus_Request,
////                \App\Models\Trade::WithdrawStatus_Confirmed,
////                \App\Models\Trade::WithdrawStatus_Processing,
////                \App\Models\Trade::WithdrawStatus_Success,
////                \App\Models\Trade::WithdrawStatus_Failed,
//            );
//            $idx = mt_rand(0, count($items)-1);
//            return $items[ $idx ];
//        },
//        'withdraw_id' => null,
//        'withdraw_money' => mt_rand(1, 1000),
//        'platform_money' => mt_rand(1, 1000),
//        'platform_fee_rate' => mt_rand(1, 1000),
//
//        'card_id' => mt_rand(18000000, 28000000),
//        'card_pid' => mt_rand(18000000, 28000000),
//        'card_money' => mt_rand(1, 10000),
    ];
});
