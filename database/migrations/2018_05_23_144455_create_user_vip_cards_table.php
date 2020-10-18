<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVipcardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_vip_cards', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');

            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('用户编号');
            $table->unsignedBigInteger('owner_id')->nullable()->index()->comment('拥有者编号');
            $table->unsignedBigInteger('device_id')->nullable()->index()->comment('设备编号');
            $table->unsignedBigInteger('trade_id')->nullable()->index()->comment('交易号');
            $table->unsignedBigInteger('log_user_login_id')->nullable()->comment('用户登录记录');
            $table->string('user_openid')->nullable()->index()->comment('微信用户的openid');
            $table->string('goods_name')->nullable()->comment('商品名称');

            $table->unsignedInteger('used_count')->default(1)->comment('已用次数');
            $table->unsignedInteger('total_count')->default(1)->comment('可用次数');
            $table->unsignedInteger('seconds')->default(60)->comment('可用时长');
            $table->unsignedInteger('days')->default(1)->comment('有效期');
            $table->timestamp('expiration')->nullable()->comment('到期时间');

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
        Schema::dropIfExists('user_vip_cards');
    }
}
