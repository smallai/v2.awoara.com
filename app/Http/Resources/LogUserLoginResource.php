<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

class LogUserLoginResource extends Resource
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
            'ip' => $this->ip,
            'src' => $this->src,
            'remark' => $this->remark,
            'login_at' => ''.$this->login_at,
            'device' => $this->device,
            'platform' => $this->platform,
            'browser' => $this->browser,
            'platform_version' => $this->platform_version,
            'browser_version' => $this->browser_version,
            'created_at' => ''.$this->created_at,
            'updated_at' => ''.$this->updated_at,
            'deleted_at' => $this->when($this->deleted_at, function () {
                return ''.$this->deleted_at;
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
        ];
        return $data;
    }
}
