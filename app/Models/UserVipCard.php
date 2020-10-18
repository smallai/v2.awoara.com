<?php

namespace App\Models;

use App\Utils\ModelFiledTranslator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\UserVipCard
 *
 * @property int $id 编号
 * @property int|null $user_id 用户编号
 * @property int|null $owner_id 拥有者编号
 * @property int|null $device_id 设备编号
 * @property int|null $trade_id 交易号
 * @property int|null $log_user_login_id 用户登录记录
 * @property int $seconds 可用时长
 * @property int $used_count 可用次数
 * @property int $days 有效期
 * @property \Illuminate\Support\Carbon|null $expiration 到期时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $user_openid
 * @property int|null $total_count
 * @property string|null $goods_name
 * @property int $today_limit 当天可用次数
 * @property-read \App\Models\Device|null $device
 * @property-read \App\Models\LogUserLogin $logLogin
 * @property-read \App\Models\User|null $owner
 * @property-read \App\Models\Trade $trade
 * @property-read \App\Models\User|null $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserVipCard onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereLogUserLoginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereTodayLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereTotalCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereTradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereUsedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVipCard whereUserOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserVipCard withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserVipCard withoutTrashed()
 * @mixin \Eloquent
 */
class UserVipCard extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    protected $table = 'user_vip_cards';

    protected $fillable = [
        'id',
        'user_id',
        'owner_id',
        'device_id',
        'trade_id',
        'log_user_login_id',
        'user_openid',

        'seconds',
        'days',
        'used_count',
        'total_count',
        'expiration',
        'goods_name',
        'today_limit',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'owner_id' => 'integer',
        'device_id' => 'integer',
        'trade_id' => 'integer',
        'log_user_login_id' => 'integer',
        'user_openid' => 'string',
        'seconds' => 'integer',
        'count' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'expiration',
        'begin_time',
        'end_time',
    ];

    static public $filed_text = [
        'id' => '编号',
        'user_id' => '用户编号',
        'owner_id' => '拥有者编号',
        'device_id' => '网点',
        'trade_id' => '交易号',
        'log_user_login_id' => '登录记录号',
        'user_openid' => '用户的openid',

        'goods_name' => '名称',
        'seconds' => '可用时长',
        'count' => '可用次数',
        'expiration' => '过期时间',

        'used_count' => '已用次数',
        'total_count' => '可用次数',
        'today_limit' => '当天可用次数',

        'created_at' => '购买时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
        'begin_time' => '生效时间',
        'end_time' => '过期时间',
    ];

    static public $filed_value_text = [

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
        return $this->hasOne(Trade::class);
    }

    public function logLogin()
    {
        return $this->hasOne(LogUserLogin::class);
    }

    public function leftTime()
    {
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $this->expiration);
        $left = $end->timestamp - Carbon::now()->timestamp;
        Log::debug('leftTime', [$end->timestamp, Carbon::now()->timestamp, $left]);
        return $left;
    }

    public function scopeLocal($query)
    {
        $user = \Auth::user();
        if (isset($user))
        {
            if ($user->hasRole('superadmin'))
                $query;
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

//    //    全局过滤器
//    public static function boot()
//    {
//        parent::boot();
//
////        static::addGlobalScope(new OwnerScope());
//    }
}
