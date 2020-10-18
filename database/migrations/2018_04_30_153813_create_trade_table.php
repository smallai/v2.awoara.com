<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');

//            关联查询
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('用户编号');
            $table->unsignedBigInteger('owner_id')->nullable()->index()->comment('设备拥有者');
            $table->unsignedBigInteger('device_id')->nullable()->index()->comment('设备编号');
            $table->unsignedBigInteger('washcar_id')->nullable()->index()->comment('洗车记录编号');
            $table->unsignedBigInteger('goods_id')->nullable()->index()->commnet('商品编号');
            $table->unsignedBigInteger('withdraw_id')->nullable()->index()->comment('提现记录号');
            $table->unsignedBigInteger('log_user_login_id')->nullable()->index()->comment('登录记录');
            $table->unsignedBigInteger('device_payment_id')->nullable()->index()->comment('设备端生成的支付号');

            //付款方式，付款状态，确认状态，退款状态，提现状态
            $table->tinyInteger('payment_type')->nullable()->default(\App\Models\Trade::PaymentType_None)->index()->comment('支付类型');
            $table->tinyInteger('payment_status')->nullable()->default(\App\Models\Trade::PaymentStatus_None)->index()->comment('支付状态');
            $table->tinyInteger('confirm_status')->nullable()->default(\App\Models\Trade::GoodsStatus_None)->index()->comment('收货状态');
            $table->tinyInteger('refund_status')->nullable()->default(\App\Models\Trade::RefundStatus_None)->index()->comment('退款状态');
            $table->tinyInteger('withdraw_status')->nullable()->default(\App\Models\Trade::WithdrawStatus_None)->index()->comment('提现状态');

//            用户信息
            $table->ipAddress('user_ip')->nullable()->comment('产生记录的IP地址');
            $table->string('user_phone')->nullable()->comment('用户手机');
            $table->string('user_email')->nullable()->comment('用户邮箱');
            $table->string('user_openid')->nullable()->comment('用户的第三方ID');

//            商品信息
            $table->tinyInteger('is_self')->nullable()->default(0)->comment('是否自营');
            $table->string('goods_name')->nullable()->commnet('名称');
            $table->unsignedInteger('goods_price')->nullable()->default(0)->comment('价格');
            $table->string('goods_image')->nullable()->commnet('商品图片');
            $table->tinyInteger('goods_is_sale')->default(true)->index()->comment('是否上架');
            $table->tinyInteger('goods_is_recommend')->default(false)->comment('是否推荐');
            $table->unsignedInteger('goods_seconds')->default(60)->comment('可用时长');
            $table->unsignedInteger('goods_count')->default(1)->comment('可用次数');
            $table->unsignedInteger('goods_days')->default(1)->commnet('有效期');

            $table->time('confirmed_at')->nullable()->comment('推送成功时间');

//            付款信息
            $table->string('payment_trade_id')->nullable()->index()->comment('支付渠道交易号');
            $table->unsignedInteger('payment_money')->nullable()->default(0)->comment('支付金额');
            $table->timestamp('payment_at')->nullable()->comment('支付时间');
            $table->string('payment_signature')->nullable()->comment('付款签名');

//            退款信息
            $table->unsignedInteger('refund_money')->default(0)->comment('退款金额');
            $table->string('refund_remark')->default('')->comment('退款备注');
            $table->timestamp('refund_at')->nullable()->comment('退款时间');
            $table->integer('refund_code')->nullable()->comment('退款返回码');
            $table->string('refund_signature')->nullable()->comment('退款签名');

//            卡品信息
            $table->unsignedBigInteger('card_id')->nullable()->comment('卡号');
            $table->unsignedBigInteger('card_pid')->nullable()->comment('卡内码');
            $table->unsignedInteger('card_money')->nullable()->comment('卡内余额');

//            提现信息
            $table->unsignedInteger('withdraw_money')->nullable()->default(0)->comment('提现金额');
            $table->timestamp('withdraw_at')->nullable()->comment('提现时间');

//            平台信息
            $table->unsignedInteger('platform_money')->nullable()->default(0)->comment('平台收入');
            $table->unsignedInteger('platform_fee_rate')->nullable()->default(0)->comment('平台费率');

            $table->string('signature')->nullable()->comment('签名');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
