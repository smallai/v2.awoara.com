<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TradeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);

        $data = [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'owner_id' => $this->owner_id,
            'device_id' => $this->device_id,
            'washcar_id' => $this->washcar_id,
            'goods_id' => $this->goods_id,
            'withdraw_id' => $this->withdraw_id,
            'log_user_login_id' => $this->log_user_login_id,
            'device_payment_id' => $this->device_payment_id,

            'payment_type' => $this->payment_type,
            'payment_status' => $this->payment_status,
            'confirm_status' => $this->confirm_status,
            'refund_status' => $this->refund_status,
            'withdraw_status' => $this->withdraw_status,

//            'user_ip' => $this->user_ip,
//            'user_phone' => $this->user_phone,
//            'user_email' => $this->user_email,
//            'user_openid' => $this->user_openid,
            $this->mergeWhen($this->user_id, function () {
                return [
                    'user_ip' => $this->user_ip,
                    'user_phone' => $this->user_phone,
                    'user_email' => $this->user_email,
                    'user_openid' => $this->user_openid,
                ];
            }),

            'is_self' => $this->is_self,
            'goods_name' => $this->goods_name,
            'goods_price' => $this->goods_price,
            'goods_image' => $this->goods_image,
            'goods_is_sale' => $this->goods_is_sale,
            'goods_is_recommend' => $this->goods_is_recommend,
            'goods_seconds' => $this->goods_seconds,
            'goods_count' => $this->goods_count,
            'goods_days' => $this->goods_days,

            $this->mergeWhen($this->payment_at, function () {
                return [
                    'payment_trade_id' => $this->payment_trade_id,
                    'payment_money' => $this->payment_money,
                    'payment_at' => $this->payment_at,
//            'payment_signature' => $this->payment_signature,
                ];
            }),

            'confirmed_at' => $this->confirmed_at,

            $this->mergeWhen($this->refund_at, function () {
                return [
                    'refund_money' => $this->refund_money,
                    'refund_remark' => $this->refund_remark,
                    'refund_at' => $this->refund_at,
                    'refund_code' => $this->refund_code,
                ];
            }),

            $this->mergeWhen($this->card_id, function () {
                return [
                    'card_id' => $this->card_id,
                    'card_pid' => $this->card_pid,
                    'card_money' => $this->card_money,
                ];
            }),

            'withdraw_money' => $this->withdraw_money,
            'withdraw_at' => $this->withdraw_at,

            'platform_money' => $this->platform_money,
            'platform_fee_rate' => $this->platform_fee_rate,

//            'signature' => $this->signature,
            'created_at' => ''.$this->created_at,
            'updated_at' => ''.$this->updated_at,
            'deleted_at' => $this->when($this->deleted_at, function () {
                return ''.$this->deleted_at;
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'owner' => $this->whenLoaded('owner', function () {
                return new UserResource($this->owner);
            }),
            'device' => $this->whenLoaded('device', function () {
                return new DeviceResource($this->device);
            }),
            'goods' => $this->whenLoaded('goods', function () {
                return new GoodsResource($this->goods);
            }),
            'washcar' => $this->whenLoaded('washcar', function () {
                return new WashCarResource($this->washcar);
            })
        ];
        return $data;
    }
}
