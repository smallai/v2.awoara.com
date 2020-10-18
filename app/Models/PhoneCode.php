<?php

namespace App\Models;

/**
 * App\Models\PhoneCode
 *
 * @property int $id
 * @property int|null $device_id 用户扫码的设备ID
 * @property string $client_ip 用户的IP
 * @property string|null $browser 用户的浏览器
 * @property string|null $device 用户的手机
 * @property string|null $platform 用户的系统
 * @property string $phone 手机号码
 * @property string $code 验证码
 * @property string|null $template 模板编号
 * @property string|null $sign_name 签名
 * @property \Illuminate\Support\Carbon $expiration 过期时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereClientIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereSignName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PhoneCode extends Model
{
    protected $table = 'phone_codes';

    protected $fillable = [
        'id',
        'device_id',
        'client_ip',
        'browser',
        'device',
        'platform',

        'phone',
        'code',
        'template',
        'sign_name',
        'expiration',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expiration',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expiration' => 'datetime',
    ];
}
