<?php

namespace App\Models;

/**
 * App\Models\PhoneVerificationCode
 *
 * @property int $id
 * @property int|null $device_id 用户扫码的设备ID
 * @property string|null $client_ip 用户的IP
 * @property string|null $browser 用户的浏览器
 * @property string|null $device 用户的手机
 * @property string|null $platform 用户的系统
 * @property string $phone 手机号码
 * @property string $code 验证码
 * @property string|null $template 模板编号
 * @property string|null $sign_name 签名
 * @property \Illuminate\Support\Carbon|null $expiration 过期时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereClientIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereSignName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneVerificationCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PhoneVerificationCode extends Model
{
    protected $table = 'phone_verification_codes';

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
        'phone' => 'string',
        'code' => 'string',
    ];
}
