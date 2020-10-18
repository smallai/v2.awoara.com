<?php

namespace App\Models;

use App\Utils\ModelFiledTranslator;

/**
 * App\Models\Visitor
 *
 * @property int $id
 * @property string $ip
 * @property string|null $device 设备
 * @property string|null $platform 平台
 * @property string|null $browser 浏览器
 * @property string|null $platform_version 平台版本
 * @property string|null $browser_version 浏览器版本
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type 类型
 * @property string $agent 浏览器标识
 * @property string $session_id 回话ID
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereBrowserVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor wherePlatformVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Visitor extends Model
{
    use ModelFiledTranslator;

    protected $table = 'visitors';

    protected $fillable = [
        'id',
        'ip',
        'type',
        'agent',
        'session_id',
        'device',
        'platform',
        'browser',
        'platform_version',
        'browser_version'
    ];

    protected $dates = [
        'login_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    static protected $filed_text = [
        'id' => '编号',
        'ip' => 'IP地址',
        'type' => '类型',
        'agent' => '浏览器标识',
        'session_id' => '会话标识',
        'device' => '设备',
        'platform' => '平台',
        'browser' => '浏览器',
        'platform_version' => '平台版本',
        'browser_version' => '浏览器版本',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
    ];

    static protected $filed_value_text = [
    ];
}
