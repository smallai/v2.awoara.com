<?php

namespace App\Models;

use App\Scopes\DeviceScope;
use App\Utils\ModelFiledTranslator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Device
 *
 * @property int $id 编号
 * @property int|null $owner_id 设备的拥有者
 * @property string|null $product_key 阿里云的产品标识
 * @property string|null $device_name 阿里云的设备名称
 * @property string|null $device_secret 阿里云的设备密钥
 * @property string|null $company_logo 公司LOGO
 * @property string|null $company_name 公司名称
 * @property string|null $company_address 公司地址
 * @property string|null $company_phone 公司电话
 * @property string|null $company_site 公司网站
 * @property string|null $province 省
 * @property string|null $city 市
 * @property string|null $district 区
 * @property string|null $street 街道
 * @property string|null $name 名称
 * @property string|null $address 地址
 * @property string|null $phone 联系电话
 * @property int|null $platform_fee_rate 平台费费率，千分之一
 * @property bool|null $is_self 是否自营
 * @property bool|null $is_buyer 已购买
 * @property bool|null $is_online 网络
 * @property int|null $status 设备状态
 * @property array|null $settings 配置
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $vip_card_today_limit 会员卡每天可用次数
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trade[] $canWithdrawMoney
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Goods[] $goods
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trade[] $income
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trade[] $refunds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trade[] $trade
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WashCar[] $washcar
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WithdrawMoney[] $withdraw
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCompanyAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCompanyLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCompanyPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCompanySite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDeviceSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereIsBuyer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereIsSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device wherePlatformFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereProductKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device whereVipCardTodayLimit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Device withoutTrashed()
 * @mixin \Eloquent
 */
class Device extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    const DeviceStatus_Online  = 1;
    const DeviceStatus_Offline = 2;
    const DeviceStatus_Disable = 3;

    protected $table = 'devices';

    protected $guard_name = 'web';

    protected $fillable = [
        'id',
        'product_key',
        'device_name',
        'device_secret',
        'owner_id',
        'name',
        'address',
        'phone',
        'platform_fee_rate',
        'status',
        'is_self',
        'is_buyer',
        'is_online',
        'settings',
        'vip_card_today_limit',
    ];

    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'platform_fee_rate' => 'integer',
        'status' => 'integer',
        'settings' => 'json',
        'is_self' => 'boolean',
        'is_buyer' => 'boolean',
        'is_online' => 'boolean',
    ];

    //字段对应的中文意思
    static protected $filed_text = [
        'id' => '编号',
        'owner_id' => '管理员',

        'product_key' => '产品标识',
        'device_name' => '设备标识',
        'device_secret' => '设备密钥',

        'company_logo' => '公司图标',
        'company_name' => '公司名称',
        'company_address' => '公司地址',
        'company_phone' => '公司电话',
        'company_site' => '公司网站',

        'name' => '名称',
        'address' => '地址',
        'phone' => '联系电话',
        'platform_fee_rate' => '平台费率', //(‰)
        'status' => '状态',
        'is_self' => '自营',
        'is_online' => '网络',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
        'vip_card_today_limit' => '会员卡当天可用次数',
    ];

    //字段值对应的中文意思
    static protected $filed_value_text = [
        'status' => [
            Device::DeviceStatus_Online => '在线',
            Device::DeviceStatus_Offline => '营业',
            Device::DeviceStatus_Disable => '关闭',
        ],
        'is_online' => [
            1 => '在线',
            0 => '离线',
        ],
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /*
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * */
    public function goods() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Goods::class, 'device_id', 'id');
    }

    public function washcar()
    {
        return $this->hasMany(WashCar::class);
    }

    public function income()
    {
        return $this->hasMany(Trade::class)->whereIn('payment_type', [
            Trade::PaymentType_Coin,
            Trade::PaymentType_Banknote,
            Trade::PaymentType_Alipay,
            Trade::PaymentType_WeChat,
        ]);
    }

//    收款记录
    public function trade()
    {
        return $this->hasMany(Trade::class);
    }

//    退款记录
    public function refunds()
    {
        return $this->hasMany(Trade::class);
    }

//    提现记录
    public function withdraw()
    {
        return $this->hasMany(WithdrawMoney::class);
    }

//    可提现状态的记录
    public function canWithdrawMoney()
    {
        return $this->hasMany(Trade::class)
            ->whereIn('payment_type', [Trade::PaymentType_Alipay, Trade::PaymentType_WeChat])
            ->where('payment_status', Trade::PaymentStatus_Success)
            ->where('withdraw_status', [ Trade::WithdrawStatus_None ])
            ->whereIn('refund_status', [ Trade::RefundStatus_None, Trade::RefundStatus_Success ])
            ->where('payment_at', '>', Carbon::now()->addDays(-1));
    }

    public function scopeLocal($query)
    {
        $user = Auth::user();
        if (isset($user))
        {
            if ($user->hasRole('superadmin'))
                ;
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

////    收到的硬币/纸币金额
//    public function banknoteRecords($type, $date = null)
//    {
//        if (is_null($date))
//            $date = Carbon::today();
//
//        return Banknote::where('device_id', $this->id)
//            ->where('type', $type)
//            ->whereDate('time', $date)
//            ->get();
//    }
//
////    根据收款状态查询
//    public function paymentRecords($payment_status, $date = null)
//    {
//        if (is_null($date))
//            $date = Carbon::today();
//
//        return Trade::where('device_id', $this->id)
//            ->where('payment_status', $payment_status)
//            ->whereDate('payment_at', $date)
//            ->get();
//    }
//
//    //根据退款状态查询
//    public function refundRecords($refund_status, $date = null)
//    {
//        if (is_null($date))
//            $date = Carbon::today();
//
//        return Trade::where('device_id', $this->id)
//            ->where('refund_status', $refund_status)
//            ->whereDate('refund_at', $date)
//            ->get();
//    }
//
////    根据提现状态查询
//    public function cashRecords($cash_status, $date = null)
//    {
//        if (is_null($date))
//            $date = Carbon::today();
//
//        return Trade::where('device_id', $this->id)
//            ->where('cash_status', $cash_status)
//            ->whereDate('refund_at', $date)
//            ->get();
//    }
//
//    public function canCashRecord($beginDate = null, $endDate = null)
//    {
//        $builder = Trade::where('device_id', $this->id);
//        if (!is_null($beginDate))
//            $builder->whereDate('payment_at', '>=', $beginDate);
//        if (!is_null($endDate))
//            $builder->whereDate('payment_at', '<=', $beginDate);
//        return $builder->where('payment_status', Trade::PaymentStatus_Success)
//            ->whereNotIn('refund_status', [Trade::RefundStatus_Processing])
//            ->where('cash_status', Trade::WithdrawStatus_None)
//            ->get();
//    }
//
//    public function washCarRecord($date = null)
//    {
//        if (is_null($date))
//            $date = Carbon::today();
//
//        return WashCar::where('device_id', $this->id)
//            ->whereDate('begin_at', $date)
//            ->get();
//    }

//    public function scopeCanEdit($query)
//    {
//        $user = Auth::user();
//        if (!$user->hasRole('superadmin'))
//            $query->where('id', $user->id);
//    }

////    全局过滤器
//    static public function boot()
//    {
//        parent::boot();
//
////        static::addGlobalScope(new DeviceScope());
//    }
}
