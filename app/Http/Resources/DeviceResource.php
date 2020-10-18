<?php

namespace App\Http\Resources;

use App\Models\Device;
use App\Models\User;
use function foo\func;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

class DeviceResource extends Resource
{
    public function user() : User
    {
        return Auth::guard('api')->user();
    }

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
            $this->mergeWhen($this->user()->isSuperAdmin(), [
                'product_key' => $this->product_key,
                'device_name' => $this->device_name,
                'device_secret' => $this->device_secret,
            ]),
            'owner_id' => $this->owner_id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'platform_fee_rate' => $this->platform_fee_rate,
            'status' => $this->status,
            'is_self' => $this->is_self,
            'settings' => $this->settings,
            'created_at' => ''.$this->created_at,
            'updated_at' => ''.$this->updated_at,
            'deleted_at' => $this->when($this->deleted_at, function () {
                return ''.$this->deleted_at;
            }),

            'owner' => $this->whenLoaded('owner', function () {
                return new UserResource($this->owner);
            }),
        ];
        return $data;
    }
}
