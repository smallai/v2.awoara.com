<?php

namespace App\Models;

use App\Scopes\LogUserLoginScope;
use App\Utils\ModelFiledTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\LogUserLogin
 *
 * @property int $id 编号
 * @property int $user_id 用户编号
 * @property \Illuminate\Support\Carbon $login_at 登录时间
 * @property string $ip 登录IP
 * @property string $src 登录方式
 * @property string $remark
 * @property string|null $device 设备
 * @property string|null $platform 平台
 * @property string|null $browser 浏览器
 * @property string|null $platform_version 平台版本
 * @property string|null $browser_version 浏览器版本
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\LogUserLogin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereBrowserVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin wherePlatformVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogUserLogin whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\LogUserLogin withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\LogUserLogin withoutTrashed()
 * @mixin \Eloquent
 */
class LogUserLogin extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    protected $table = 'log_user_login';

    protected $fillable = [
        'id',
        'user_id',
        'ip',
        'src',
        'remark',
        'login_at',
        'device',
        'platform',
        'browser',
        'platform_version',
        'browser_version'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'login_at',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    static protected $filed_text = [
        'id' => '编号',
        'user_id' => '用户编号',
        'ip' => 'IP地址',
        'src' => '来源',
        'remark' => '备注',
        'login_at' => '登录时间',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
        'device' => '设备',
        'platform' => '平台',
        'browser' => '浏览器',
        'platform_version' => '平台版本',
        'browser_version' => '浏览器版本'
    ];

    static protected $filed_value_text = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLocal($query)
    {
        $user = Auth::user();
        if (isset($user))
        {
            if ($user->hasRole('superadmin'))
                $query->withTrashed();
            else if ($user->hasRole('admin'))
                $query->where('user_id', $user->id);
            else
                $query->where('id', PHP_INT_MIN);
        }
        else
        {
            $query->where('id', PHP_INT_MIN);
        }
    }

////    全局过滤器
//    static public function boot()
//    {
//        parent::boot();
//
////        static::addGlobalScope(new LogUserLoginScope());
//    }
}
