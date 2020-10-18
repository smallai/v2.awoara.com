<?php

namespace App\Utils;

class IdGenerator
{
    protected static function getId()
    {
        $id = date('ymdHis').mt_rand(100, 999);
        return $id;
    }

//    用户编号
    public static function userId()
    {
        $id = IdGenerator::getId().'0';
        return $id;
    }

//    订单编号
    public static function orderId()
    {
        $id = IdGenerator::getId().'1';
        return $id;
    }

//    交易编号
    public static function tradeId()
    {
        $id = IdGenerator::getId().'2';
        return $id;
    }

//    退款编号
    static function refundId()
    {
        $id = IdGenerator::getId().'3';
        return $id;
    }

//    提现编号
    public static function withdrawId()
    {
        $id = IdGenerator::getId().'4';
        return $id;
    }

    public static function vipCardId() {
        $id = IdGenerator::getId().'5';
        return $id;
    }
}
