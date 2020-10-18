<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * phone: 手机号码
         * content： 发送的内容
         * return_code： 短信发送平台的返回码
         * code： 发送的验证码
         * ip： 接收验证码的人的IP地址
        */
        Schema::create('log_sms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone')->default('');
            $table->string('content')->default('');
            $table->string('return_code')->default('');
            $table->string('code')->default('');
            $table->ipAddress('ip')->nullable();
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
        Schema::dropIfExists('log_sms');
    }
}
