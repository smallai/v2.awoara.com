<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class WashCarResource extends Resource
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

            'used_seconds' => $this->used_seconds,
            'total_seconds' => $this->total_seconds,
            'free_seconds' => $this->free_seconds,

            'water_seconds' => $this->water_seconds,
            'cleaner_seconds' => $this->cleaner_seconds,
            'tap_switch_seconds' => $this->tap_switch_seconds,
            'water_count' => $this->water_count,
            'cleaner_count' => $this->cleaner_count,
            'tap_switch_count' => $this->tap_switch_count,
            'temperature' => $this->temperature,

            'begin_at' => $this->begin_at,
            'end_at' => $this->end_at,

            'created_at' => ''.$this->created_at,
            'updated_at' => ''.$this->updated_at,
            'deleted_at' => $this->when($this->deleted_at, function () {
                return ''.$this->deleted_at;
            }),

            'device' => $this->whenLoaded('device', function () {
                return new DeviceResource($this->device);
            }),
            'owner' => $this->whenLoaded('owner', function () {
                return new UserResource($this->owner);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'trade' => $this->whenLoaded('trade', function () {
                return new TradeCollection($this->trade);
            }),
        ];
        return $data;
    }
}
