<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('device_id')->nullable()->comment('用户扫码的设备ID');

            $table->ipAddress('client_ip')->comment('用户的IP');
            $table->string('browser')->nullable()->comment('用户的浏览器');
            $table->string('device')->nullable()->comment('用户的手机');
            $table->string('platform')->nullable()->comment('用户的系统');
            $table->string('phone')->comment('手机号码');
            $table->string('code')->comment('验证码');
            $table->string('template')->nullable()->comment('模板编号');
            $table->string('sign_name')->nullable()->comment('签名');

            $table->timestamp('expiration')->comment('过期时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_codes');
    }
}
