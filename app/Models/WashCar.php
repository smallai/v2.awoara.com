<?php

namespace App\Models;

use App\Scopes\WashCarScope;
use App\Utils\ModelFiledTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\WashCar
 *
 * @property int $id 编号
 * @property int|null $user_id 用户编号
 * @property int|null $owner_id 记录拥有者
 * @property int|null $device_id 设备编号
 * @property int|null $washcar_count 设备生成的洗车次数
 * @property int $used_seconds 使用时长
 * @property int $total_seconds 可用时长
 * @property int $free_seconds 最大空闲时间
 * @property int $water_seconds 水泵使用时长
 * @property int $cleaner_seconds 吸尘器使用时长
 * @property int $tap_switch_seconds 水龙头使用时长
 * @property int $water_count 水泵开关次数
 * @property int $cleaner_count 吸尘器开关次数
 * @property int $tap_switch_count 水龙头开关次数
 * @property int $temperature 温度
 * @property \Illuminate\Support\Carbon|null $begin_at 开始时间
 * @property \Illuminate\Support\Carbon|null $end_at 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Device|null $device
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trade[] $trade
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar date($date)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WashCar onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereCleanerCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereCleanerSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereFreeSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereTapSwitchCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereTapSwitchSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereTotalSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereUsedSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereWashcarCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereWaterCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WashCar whereWaterSeconds($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WashCar withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WashCar withoutTrashed()
 * @mixin \Eloquent
 */
class WashCar extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    protected $table = 'washcar';

    protected $fillable = [
        'id',
        'user_id',
        'owner_id',
        'device_id',

        'used_seconds',
        'total_seconds',
        'free_seconds',

        'water_seconds',
        'cleaner_seconds',
        'tap_switch_seconds',
        'water_count',
        'cleaner_count',
        'tap_switch_count',
        'temperature',

        'begin_at',
        'end_at',
    ];

    protected $dates = [
        'begin_at',
        'end_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'owner_id' => 'integer',
        'device_id' => 'integer',

        'used_seconds' => 'integer',
        'total_seconds' => 'integer',
        'free_seconds' => 'integer',

        'water_seconds' => 'integer',
        'cleaner_seconds' => 'integer',
        'tap_switch_seconds' => 'integer',
        'water_count' => 'integer',
        'cleaner_count' => 'integer',
        'tap_switch_count' => 'integer',
        'temperature' => 'integer',
    ];

    static protected $filed_text = [
        'id' => '编号',
        'device_id' => '设备',
        'trade_id' => '交易记录',
        'used_seconds' => '使用时长',
        'total_seconds' => '可用时长',
        'free_seconds' => '空闲时长',

        'water_seconds' => '水泵时长',
        'cleaner_seconds' => '吸尘时长',
        'tap_switch_seconds' => '水龙头时长',
        'water_count' => '水泵次数',
        'cleaner_count' => '吸尘次数',
        'tap_switch_count' => '水龙头次数',
        'temperature' => '温度',

        'begin_at' => '开始时间',
        'end_at' => '结束时间',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
    ];

    static protected $filed_value_text = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function trade()
    {
        return $this->hasMany(Trade::class, 'washcar_id');
    }

    public function scopeDate($query, $date)
    {
        return $query->whereDate('begin_at', $date);
    }

    public function scopeLocal($query)
    {
        $user = Auth::user();
        if (isset($user))
        {
            if ($user->hasRole('superadmin'))
                $query->withTrashed();
            else if ($user->hasRole('admin'))
                $query->where('owner_id', $user->id);
            else
                $query->where('id', PHP_INT_MIN);
        }
        else
        {
            $query->where('id', PHP_INT_MIN);
        }
    }

//    全局过滤器
    static public function boot()
    {
        parent::boot();

//        static::addGlobalScope(new WashCarScope());
    }
}
