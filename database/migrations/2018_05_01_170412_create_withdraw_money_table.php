<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_money', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->unsignedBigInteger('device_id')->nullable()->comment('设备编号');
            $table->unsignedBigInteger('owner_id')->default(0)->index()->comment('提现人编号');
            $table->unsignedBigInteger('log_user_login_id')->nullable()->comment('提现人登录记录编号');

            $table->tinyInteger('withdraw_status')->nullable()->index()->comment('提现状态');
            $table->timestamp('withdraw_at')->nullable()->comment('提现时间');

            $table->ipAddress('owner_ip')->nullable()->comment('IP地址');
            $table->string('owner_phone')->nullable()->comment('提现人手机');
            $table->string('owner_email')->nullable()->comment('提现人邮箱');
            $table->string('owner_payee')->nullable()->default('')->comment('收款人');
            $table->string('owner_real_name')->nullable()->default('')->comment('收款人真实姓名');

            $table->unsignedInteger('payment_money')->nullable()->default(0)->comment('支付金额');
            $table->unsignedInteger('refund_money')->nullable()->default(0)->comment('退款金额');
            $table->unsignedInteger('withdraw_money')->nullable()->default(0)->comment('提现金额就是用户收入');
            $table->unsignedInteger('platform_money')->nullable()->default(0)->comment('平台收入');
            $table->unsignedInteger('platform_fee_rate')->nullable()->default(0)->comment('平台费率');

            $table->string('payer_show_name')->nullable()->comment('付款名称');
            $table->string('payer_remark')->nullable()->comment('付款备注');

            $table->tinyInteger('payment_type')->nullable()->comment('支付渠道');
            $table->string('payment_trade_id')->nullable()->comment('支付渠道的交易号');
            $table->unsignedInteger('payment_code')->nullable()->comment('支付渠道返回码');
            $table->string('payment_msg')->nullable()->comment('支付渠道返回消息');
            $table->timestamp('payment_at')->nullable()->comment('付款时间');

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
        Schema::dropIfExists('withdraw_money');
    }
}
