<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->unsignedBigInteger('register_device_id')->nullable()->comment('注册地点');

            $table->string('name')->nullable()->comment('昵称');
            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->string('phone')->nullable()->unique()->comment('手机');
            $table->string('password')->comment('密码');

            $table->string('payee')->nullable()->comment('收款账号');
            $table->string('real_name')->nullable()->comment('真实姓名');
            $table->unsignedInteger('page_size')->default(5)->comment('分页大小');

            $table->string('api_token')->nullable()->comment('API口令登录');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
