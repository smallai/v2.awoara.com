<?php

namespace App\Http\Resources;

use function foo\func;
use Illuminate\Http\Resources\Json\Resource;

class GoodsResource extends Resource
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
            'device_id' => $this->device_id,
            'owner_id' => $this->owner_id,
            'name' => $this->name,
            'price' => $this->price,
            'image' => $this->image,
            'is_sale' => $this->is_sale,
            'is_recommend' => $this->is_recommend,
            'seconds' => $this->seconds,
            'count' => $this->count,
            'days' => $this->days,
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
        ];
        return $data;
    }
}
