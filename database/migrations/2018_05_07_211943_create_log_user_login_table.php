<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogUserLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_user_login', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->unsignedBigInteger('user_id')->index()->comment('用户编号');
            $table->timestamp('login_at')->comment('登录时间');
            $table->ipAddress('ip')->default('0.0.0.0')->comment('登录IP');
            $table->string('src')->default('')->comment('登录方式');
            $table->string('remark')->default('')->commnet('备注信息');
            $table->string('device')->nullable()->default('')->comment('设备');
            $table->string('platform')->nullable()->default('')->comment('平台');
            $table->string('browser')->nullable()->default('')->comment('浏览器');
            $table->string('platform_version')->nullable()->default('')->comment('平台版本');
            $table->string('browser_version')->nullable()->default('')->comment('浏览器版本');
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
        Schema::dropIfExists('log_user_login');
    }
}
