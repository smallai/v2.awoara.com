<?php

namespace App\Models;

use App\Utils\ModelFiledTranslator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Trade
 *
 * @property int $id 编号
 * @property int|null $user_id 用户编号
 * @property int|null $owner_id 设备拥有者
 * @property int|null $device_id 设备编号
 * @property int|null $washcar_id 洗车记录编号
 * @property int|null $goods_id
 * @property int|null $withdraw_id 提现记录号
 * @property int|null $log_user_login_id 登录记录
 * @property int|null $device_payment_id 设备端生成的支付号
 * @property int|null $payment_type 支付类型
 * @property int|null $payment_status 支付状态
 * @property int|null $confirm_status 收货状态
 * @property int|null $refund_status 退款状态
 * @property int|null $withdraw_status 提现状态
 * @property string|null $user_ip 产生记录的IP地址
 * @property string|null $user_phone 用户手机
 * @property string|null $user_email 用户邮箱
 * @property string|null $user_openid 用户的第三方ID
 * @property int|null $is_self 是否自营
 * @property string|null $goods_name
 * @property int|null $goods_price 价格
 * @property string|null $goods_image
 * @property int $goods_is_sale 是否上架
 * @property int $goods_is_recommend 是否推荐
 * @property int $goods_seconds 可用时长
 * @property int $goods_count 可用次数
 * @property int $goods_days
 * @property string|null $confirmed_at 推送成功时间
 * @property string|null $payment_trade_id 支付渠道交易号
 * @property int|null $payment_money 支付金额
 * @property \Illuminate\Support\Carbon|null $payment_at 支付时间
 * @property string|null $payment_signature 付款签名
 * @property int $refund_money 退款金额
 * @property string $refund_remark 退款备注
 * @property \Illuminate\Support\Carbon|null $refund_at 退款时间
 * @property int|null $refund_code 退款返回码
 * @property string|null $refund_signature 退款签名
 * @property int|null $card_id 卡号
 * @property int|null $card_pid 卡内码
 * @property int|null $card_money 卡内余额
 * @property int|null $withdraw_money 提现金额
 * @property string|null $withdraw_at 提现时间
 * @property int|null $platform_money 平台收入
 * @property int|null $platform_fee_rate 平台费率
 * @property string|null $signature 签名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $refund_operator_id 退款的操作员
 * @property string|null $refund_id 退款记录号
 * @property array|null $meta
 * @property-read \App\Models\Device|null $device
 * @property-read \App\Models\Goods $goods
 * @property-read \App\Models\User|null $owner
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\WashCar|null $washcar
 * @property-read \App\Models\WithdrawMoney|null $withdraw
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade canWithdrawMoney()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade date($date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade dateRange($begin, $end)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade money()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade needRefundMoney()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Trade onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade qrcode()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereCardMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereCardPid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereConfirmStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereDevicePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsIsRecommend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsIsSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereGoodsSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereIsSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereLogUserLoginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePaymentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePaymentMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePaymentSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePaymentTradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePlatformFeeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade wherePlatformMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundOperatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereUserEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereUserIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereUserOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereUserPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereWashcarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereWithdrawAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereWithdrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereWithdrawMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Trade whereWithdrawStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Trade withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Trade withoutTrashed()
 * @mixin \Eloquent
 */
class Trade extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    const TradeType_Order = 1;          //用户付款
    const TradeType_Refund = 2;         //退款操作
    const TradeType_WithdrawMoney = 3;  //运营操作

//    支付状态
    const PaymentStatus_None = 0;          //未执行操作
    const PaymentStatus_Processing = 1;    //等待付款
    const PaymentStatus_Success = 2;       //付款成功
    const PaymentStatus_Failed = 3;        //付款失败

//    支付方式
    const PaymentType_None = 0;
    const PaymentType_Alipay = 1;   //支付宝付款
    const PaymentType_WeChat = 2;   //微信付款
    const PaymentType_Coin = 3;     //硬币付款
    const PaymentType_Banknote = 4; //纸币付款
    const PaymentType_IcCard = 5;   //刷卡付款
    const PaymentType_VipCard = 6;  //会员卡付款

//    收货状态
    const GoodsStatus_None = 0;         //未收货
    const GoodsStatus_Confirmed = 1;    //已收货

//    退款状态
    const RefundStatus_None = 0;       //未执行操作
    const RefundStatus_Processing = 1; //正在处理
    const RefundStatus_Success = 2;    //处理成功
    const RefundStatus_Failed = 3;     //处理失败

//    提现状态
    const WithdrawStatus_None = 0;          //未处理
    const WithdrawStatus_Request = 1;       //用户提交了提现请求
    const WithdrawStatus_Confirmed = 2;     //平台已确认提现请求
    const WithdrawStatus_Processing = 3;    //正在处理请求
    const WithdrawStatus_Success = 4;       //处理完成
    const WithdrawStatus_Failed = 5;        //处理失败
    const WithdrawState_Disable = 6;        //不可提现

    protected $table = 'trades';

    protected $guard_name = 'web';

    protected $fillable = [
        'id',

        'user_id',
        'owner_id',
        'device_id',
        'washcar_id',
        'goods_id',
        'withdraw_id',
        'log_user_login_id',
        'device_payment_id',

        'payment_type',
        'payment_status',
        'confirm_status',
        'refund_status',
        'withdraw_status',

        'user_ip',
        'user_phone',
        'user_email',
        'user_openid',

        'is_self',
        'goods_name',
        'goods_price',
        'goods_image',
        'goods_is_sale',
        'goods_is_recommend',
        'goods_seconds',
        'goods_count',
        'goods_days',

        'payment_type',
        'payment_trade_id',
        'payment_money',
        'payment_at',
        'payment_signature',

        'confirmed_at',

        'refund_id',
        'refund_operator_id',
        'refund_money',
        'refund_remark',
        'refund_at',
        'refund_code',

        'card_id',
        'card_pid',
        'card_money',

        'withdraw_money',
        'withdraw_at',

        'platform_money',
        'platform_fee_rate',

        'signature',
    ];

    protected $hidden = [
        'signature',
        'payment_signature',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'payment_at',
        'refund_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'device_id' => 'integer',
        'owner_id' => 'integer',
        'ext_id' => 'integer',
        'log_user_login_id' => 'integer',
        'goods_id' => 'integer',

		'payment_status' => 'integer',
		'confirm_status' => 'integer',
		'refund_status' => 'integer',
		'withdraw_status' => 'integer',

        'payment_money' => 'integer',
        'refund_money' => 'integer',
        'withdraw_money' => 'integer',
        'platform_money' => 'integer',
        'platform_fee_rate' => 'integer',

        'card_id' => 'integer',
        'card_pid' => 'integer',
        'card_money' => 'integer',
        'meta' => 'array',
    ];

    static protected $filed_text = [
        'id' => '编号',

        'user_id' => '用户',
        'owner_id' => '拥有者',
        'device_id' => '设备',
        'washcar_id' => '洗车',
        'goods_id' => '商品',
        'withdraw_id' => '提现',
        'log_user_login_id' => '登录记录',
        'device_payment_id' => '设备支付号',

        'payment_type' => '支付方式',
        'payment_status' => '支付状态',
        'confirm_status' => '确认状态',
        'refund_status' => '退款状态',
        'withdraw_status' => '提现状态',

        'user_ip' => '用户IP',
        'user_phone' => '用户手机',
        'user_email' => '用户邮箱',
        'user_openid' => '用户的第三方ID',

        'is_self' => '自营',
        'goods_name' => '商品名称',
        'goods_price' => '商品价格',
        'goods_image' => '商品图片',
        'goods_is_sale' => '商品上架状态',
        'goods_is_recommend' => '商品是否推荐',
        'goods_seconds' => '可用时长',
        'goods_count' => '可用次数',
        'goods_days' => '有效期',
        'goods_status' => '商品状态',

        'payment_trade_id' => '交易号',
        'payment_money' => '支付金额',
        'payment_at' => '支付时间',
        'payment_signature' => '支付签名',

        'confirmed_at' => '推送时间',

        'refund_money' => '退款金额',
        'refund_remark' => '退款原因',
        'refund_at' => '退款时间',

        'card_id' => '卡号',
        'card_pid' => '卡内码',
        'card_money' => '卡内余额',

        'withdraw_money' => '提现金额',
        'withdraw_at' => '提现时间',

        'platform_money' => '平台收入',
        'platform_fee_rate' => '平台费率',

        'signature' => '记录签名',

        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
    ];

    static protected  $filed_value_text = [
        'payment_type' => [
            Trade::PaymentType_None => '未知',
            Trade::PaymentType_Alipay => '支付宝',
            Trade::PaymentType_WeChat => '微信',
            Trade::PaymentType_Coin => '硬币',
            Trade::PaymentType_Banknote => '纸币',
            Trade::PaymentType_IcCard => '刷卡',
            Trade::PaymentType_VipCard => '会员卡',
        ],

        'payment_status' => [
            Trade::PaymentStatus_None => '未支付',
            Trade::PaymentStatus_Processing => '待付款',
            Trade::PaymentStatus_Success => '已付款',
            Trade::PaymentStatus_Failed => '付款失败',
        ],

        'confirm_status' => [
            Trade::GoodsStatus_None => '推送失败',
            Trade::GoodsStatus_Confirmed => '已推送',
        ],

        'refund_status' => [
            Trade::RefundStatus_None => '未退款',
            Trade::RefundStatus_Processing => '正在退款',
            Trade::RefundStatus_Success => '已退款',
            Trade::RefundStatus_Failed => '退款失败',
        ],

        'withdraw_status' => [
            Trade::WithdrawStatus_None => '待提现',
            Trade::WithdrawStatus_Request => '已提交',
            Trade::WithdrawStatus_Confirmed => '已通过',
            Trade::WithdrawStatus_Processing => '正在处理',
            Trade::WithdrawStatus_Success => '已提现',
            Trade::WithdrawStatus_Failed => '处理失败',
            Trade::WithdrawState_Disable => '不可提现',
        ]
    ];

//select * from `devices` where `devices`.`id` = ? and `devices`.`id` is not null limit 1
//select * from `devices` where `devices`.`id` = ? limit 1
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }

    public function washcar()
    {
        return $this->belongsTo(WashCar::class);
    }

    public function withdraw()
    {
        return $this->belongsTo(WithdrawMoney::class);
    }

//    按支付类型查询
    public function scopeType($query, $type)
    {
        if (is_array($type))
            return $query->whereIn('payment_type', $type);
        else
            return $query->where('payment_type', $type);
    }

//    扫码支付产生的记录
    public function scopeQrcode($query)
    {
        return $this->scopeType($query, [
            Trade::PaymentType_Alipay,
            Trade::PaymentType_WeChat,
        ]);
    }

//    现金收入产生的记录
    public function scopeMoney($query)
    {
        return $this->scopeType($query, [
            Trade::PaymentType_Coin,
            Trade::PaymentType_Banknote,
        ]);
    }

//    按日期查询
    public function scopeDate($query, $date = null)
    {
        if (is_null($date))
            $date = Carbon::today();
        return $query->whereDate('payment_at', $date);
    }

//    按时间段查询
    public function scopeDateRange($query, $begin, $end)
    {
        return $query->whereDate('payment_at', '>=', $begin)
            ->whereDate('payment_at', '<=', $end);
    }

    /*
     * 可提现状态的记录
     * **/
    public function scopeCanWithdrawMoney($query)
    {
        return $query->where('withdraw_status', Trade::WithdrawStatus_None)
            ->where('payment_status', Trade::PaymentStatus_Success)
//            ->where('confirm_status', Trade::GoodsStatus_Confirmed)
            ->whereIn('payment_type', [
                Trade::PaymentType_Alipay,
                Trade::PaymentType_WeChat,
            ])->whereIn('refund_status', [
                Trade::RefundStatus_None,
                Trade::RefundStatus_Success,
            ])
            ->where('payment_at', '<', Carbon::now()->addHours(-24));
    }

    /*
     * 需要退款的记录
     * */
    public function scopeNeedRefundMoney($query)
    {
        return $query->where('payment_status', Trade::PaymentStatus_Success)
            ->where('confirm_status', Trade::GoodsStatus_None)
            ->whereIn('payment_type', [
                Trade::PaymentType_Alipay,
                Trade::PaymentType_WeChat,
            ])->whereIn('refund_status', [
                Trade::RefundStatus_None,
            ])->where('withdraw_status', Trade::WithdrawStatus_None)
            ->where('payment_at', '<', Carbon::now()->addMinutes(-10));
    }

    public function updateInfo()
    {
        if (($this->payment_type === Trade::PaymentType_Alipay) || ($this->payment_type === Trade::PaymentType_WeChat))
        {
            Log::debug('order is alipay or wechat pay success');

            if (($this->payment_status === Trade::PaymentStatus_Success) && ($this->platform_fee_rate <= 1000))
            {
                Log::debug('order payment success');
                $money = $this->payment_money - $this->refund_money;
                if ($money > 0)
                {
                    $this->platform_money = (integer)(($money * $this->platform_fee_rate + 900) / 1000);
                    if ($this->platform_money < 1)
                        $this->platform_money = 1;
                    $this->withdraw_money = (integer)($money- $this->platform_money);
                    Log::debug('update money info', [
                        'platform_money' => $this->platform_money,
                        'withdraw_money' => $this->withdraw_money,
                    ]);
                }
                else if ($money < 0)
                {
                    Log::debug('money < 0');
                }
                else
                {
                    $this->platform_money = 0;
                    $this->withdraw_money = 0;
                    Log::debug('other error');
                }

                if (0 != ($this->payment_money - $this->refund_money - $this->platform_money - $this->withdraw_money))
                {
                    \Log::emergency('trade record error', [
                        'trade' => $this
                    ]);
                }
            }
            else
            {
                $this->platform_money = 0;
                $this->withdraw_money = 0;
//                Log::debug('status error');
            }
        }
        else
        {
            $this->platform_money = 0;
            $this->withdraw_money = 0;
            Log::debug('payment type error');
        }

//        针对自营产品的操作
        if ($this->is_self)
        {

        }

        $this->save();
    }

    public function hasError()
    {
        $this->updateInfo();
        $this->save();

        if ($this->payment_staus !== Trade::PaymentStatus_Success)
            return false;

        if (0 !== ($this->payment_money - $this->refund_money - $this->platform_money - $this->withdraw_money))
            return true;

        return false;
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

    public function paySign()
    {
        $text = implode('.', [
            'ai',
            $this->id,
            $this->device_id,
            $this->user_id,
            $this->owner_id,
            $this->payment_trade_id,
            $this->payment_money,
            $this->payment_at,
        ]);
        $this->payment_signature = bcrypt($text);
    }

    public function isCanRefund()
    {
        //支付成功状态
        if ($this->payment_status === Trade::PaymentStatus_Success)
        {
            //是微信或者支付宝付款的
            if (($this->payment_type === Trade::PaymentType_Alipay)
                || ($this->payment_type === Trade::PaymentType_WeChat))
            {
                //还没有提现的记录
                if ($this->withdraw_status === Trade::WithdrawStatus_None)
                {
                    if (($this->refund_status === Trade::RefundStatus_None)
                        || ($this->refund_status === Trade::RefundStatus_Failed)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

////    全局过滤器
//    static public function boot()
//    {
//        parent::boot();
//
////        static::addGlobalScope(new TradeScope());
//    }
}
