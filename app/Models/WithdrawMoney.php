<?php

namespace App\Models;

use App\Scopes\WithdrawScope;
use App\Utils\ModelFiledTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\WithdrawMoney
 *
 * @property int $id 编号
 * @property int|null $device_id 设备编号
 * @property int $owner_id 提现人编号
 * @property int|null $log_user_login_id 提现人登录记录编号
 * @property int|null $withdraw_status 提现状态
 * @property \Illuminate\Support\Carbon|null $withdraw_at 提现时间
 * @property string|null $owner_ip IP地址
 * @property string|null $owner_phone 提现人手机
 * @property string|null $owner_email 提现人邮箱
 * @property string|null $owner_payee 收款人
 * @property string|null $owner_real_name 收款人真实姓名
 * @property int|null $payment_money 支付金额
 * @property int|null $refund_money 退款金额
 * @property int|null $withdraw_money 提现金额就是用户收入
 * @property int|null $platform_money 平台收入
 * @property int|null $platform_fee_rate 平台费率
 * @property string|null $payer_show_name 付款名称
 * @property string|null $payer_remark 付款备注
 * @property int|null $payment_type 支付渠道
 * @property string|null $payment_trade_id 支付渠道的交易号
 * @property int|null $payment_code 支付渠道返回码
 * @property string|null $payment_msg 支付渠道返回消息
 * @property \Illuminate\Support\Carbon|null $payment_at 付款时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Device|null $device
 * @property-read \App\Models\LogUserLogin $logUserLogin
 * @property-read \App\Models\User $owner
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WithdrawMoney onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereLogUserLoginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereOwnerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereOwnerIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereOwnerPayee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereOwnerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereOwnerRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePayerRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePayerShowName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePaymentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePaymentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePaymentMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePaymentMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePaymentTradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePlatformFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney wherePlatformMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereRefundMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereWithdrawAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereWithdrawMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\WithdrawMoney whereWithdrawStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WithdrawMoney withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\WithdrawMoney withoutTrashed()
 * @mixin \Eloquent
 */
class WithdrawMoney extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    protected $table = 'withdraw_money';

    protected $guard_name = 'web';

    protected $fillable = [
        'id',
        'owner_id',
        'device_id',
        'log_user_login_id',

        'withdraw_status',
        'withdraw_at',

        'owner_ip',
        'owner_phone',
        'owner_email',
        'owner_payee',
        'owner_real_name',

        'payment_money',
        'refund_money',
        'withdraw_money',
        'platform_money',
        'platform_fee_rate',

        'payer_show_name',
        'payer_remark',

        'payment_type',
        'payment_trade_id',
        'payment_code',
        'payment_msg',
        'payment_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'payment_at',
        'withdraw_at',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'log_user_login_id' => 'integer',
        'payment_id' => 'integer',
        'status' => 'integer',
        'payment_code' => 'integer',
        'payment_money' => 'integer',
        'refund_money' => 'integer',
        'withdraw_money' => 'integer',
        'platform_money' => 'integer',
    ];

    static protected $filed_text = [
        'id' => '编号',
        'owner_id' => '拥有者',
        'device_id' => '设备',
        'log_user_login_id' => '登录记录',

        'withdraw_status' => '提现状态',
        'withdraw_at' => '提现时间',

        'owner_ip' => 'IP',
        'owner_phone' => '手机',
        'owner_email' => '邮箱',
        'owner_payee' => '收款账号',
        'owner_real_name' => '收款人',

        'payment_money' => '收款',
        'refund_money' => '退款',
        'withdraw_money' => '提现',
        'platform_money' => '平台费',
        'platform_fee_rate' => '费率',

        'payer_show_name' => '付款账号',
        'payer_remark' => '付款备注',

        'payment_type' => '付款方式',
        'payment_trade_id' => '交易号',
        'payment_code' => '支付返回码',
        'payment_msg' => '支付返回消息',
        'payment_at' => '支付时间',

        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
    ];

    static protected $filed_value_text = [
        'payment_type' => [
            Trade::PaymentType_None => '未知',
            Trade::PaymentType_Alipay => '支付宝',
            Trade::PaymentType_WeChat => '微信',
            Trade::PaymentType_Coin => '硬币',
            Trade::PaymentType_Banknote => '纸币',
            Trade::PaymentType_IcCard => '刷卡',
            Trade::PaymentType_VipCard => '会员卡',
        ],

        'withdraw_status' => [
            Trade::WithdrawStatus_None => '待处理',
            Trade::WithdrawStatus_Request => '已提交',
            Trade::WithdrawStatus_Confirmed => '已通过',
            Trade::WithdrawStatus_Processing => '正在处理',
            Trade::WithdrawStatus_Success => '已处理',
            Trade::WithdrawStatus_Failed => '处理失败'
        ]
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function logUserLogin()
    {
        return $this->hasOne(LogUserLogin::class, 'log_user_login_id', 'id');
    }

    public function hasError()
    {
        return 0 !== ($this->payment_money - $this->refund_money - $this->withdraw_money - $this->platform_money);
    }

    public function updateInfo()
    {
        if ($this->withdraw_status === Trade::WithdrawStatus_Success)
        {
            $money = ($this->payment_money - $this->refund_money);
            if ($money > 0)
            {
                $this->platform_fee_rate = round($this->platform_money * 1000 / $money);
            }
            else
            {
                $this->platform_fee_rate = 0;
            }
        }
        else
        {
            $this->platform_fee_rate = 0;
        }
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

//    //    全局过滤器
//    static public function boot()
//    {
//        parent::boot();
//
////        static::addGlobalScope(new WithdrawScope());
//    }
}
