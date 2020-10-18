<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
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

        $user = \Auth::guard('api')->user();
        if ($user->isOwner())
        {
            $data = [
                'id' => $this->id,
                'register_device_id' => $this->when($this->register_device_id, function () {
                    return $this->register_device_id;
                }),
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'payee' => $this->payee,
                'real_name' => $this->real_name,
                'page_size' => $this->page_size,
                'api_token' => $this->api_token,
                'created_at' => ''.$this->created_at,
                'updated_at' => ''.$this->updated_at,
                'deleted_at' => $this->when($this->deleted_at, function () {
                    return ''.$this->deleted_at;
                }),
            ];
        }

        return $data;
    }
}
