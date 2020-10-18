<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Utils\ModelFiledTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id 编号
 * @property int|null $register_device_id 注册地点
 * @property string|null $name 昵称
 * @property string|null $email 邮箱
 * @property string|null $phone 手机
 * @property string $password 密码
 * @property string|null $api_token API口令登录
 * @property string|null $payee 收款账号
 * @property string|null $real_name 真实姓名
 * @property int $page_size 分页大小
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WithdrawMoney[] $cashes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $devices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LogUserLogin[] $loginLogs
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \App\Models\Device|null $registerDevice
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trade[] $trades
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserVipCard[] $vipCard
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePageSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePayee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRegisterDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use ModelFiledTranslator;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'register_device_id',

        'name',
        'phone',
        'email',
        'password',

        'payee',
        'real_name',
        'page_size',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'register_device_id' => 'integer',
    ];

    protected $dispatchesEvents = [

    ];

    static protected $filed_text = [
        'id' => '编号',
        'register_device_id' => '注册地点',

        'name' => '名称',
        'email' => '邮箱',
        'phone' => '手机',
        'payee' => '收款账号',
        'real_name' => '真实姓名',
        'page_size' => '分页大小',

        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
    ];

    static protected $filed_value_text = [

    ];

    protected $dateFormat = 'Y-m-d H:i:sO';

    public function isSuperAdmin()
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isOwner()
    {
        $user = Auth::user();
        if (!isset($user)) {
            $user = Auth::guard('api')->user();
        }
        return isset($user) && ($user->isSuperAdmin() || ($this->id === $user->id));
    }

//    用户的月卡，季卡等。
    public function vipCard()
    {
        return $this->hasMany(UserVipCard::class);
    }

    //用户的交易记录
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    //用户的洗车记录
    public function records()
    {
        $this->hasMany(WashCar::class);
    }

    //用户的提现记录
    public function cashes()
    {
        return $this->hasMany(WithdrawMoney::class, 'owner_id');
    }

    public function loginLogs()
    {
        return $this->hasMany(LogUserLogin::class, 'user_id', 'id')->latest()->limit(20);
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'owner_id', 'id')->latest();
    }

    public function registerDevice()
    {
        return $this->belongsTo(Device::class, 'register_device_id');
    }

//    全局过滤器
    static public function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope());
    }
}
