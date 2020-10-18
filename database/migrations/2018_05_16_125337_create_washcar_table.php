<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWashcarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('washcar', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');

            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('用户编号');
            $table->unsignedBigInteger('owner_id')->nullable()->index()->comment('记录拥有者');
            $table->unsignedBigInteger('device_id')->nullable()->index()->comment('设备编号');
            $table->unsignedBigInteger('washcar_count')->nullable()->index()->comment('设备生成的洗车次数');

            $table->unsignedInteger('used_seconds')->default(0)->comment('使用时长');
            $table->unsignedInteger('total_seconds')->default(0)->comment('可用时长');
            $table->unsignedInteger('free_seconds')->default(0)->comment('最大空闲时间');

            $table->unsignedInteger('water_seconds')->default(0)->comment('水泵使用时长');
            $table->unsignedInteger('cleaner_seconds')->default(0)->comment('吸尘器使用时长');
            $table->unsignedInteger('tap_switch_seconds')->default(0)->comment('水龙头使用时长');

            $table->unsignedInteger('water_count')->default(0)->comment('水泵开关次数');
            $table->unsignedInteger('cleaner_count')->default(0)->comment('吸尘器开关次数');
            $table->unsignedInteger('tap_switch_count')->default(0)->comment('水龙头开关次数');

            $table->integer('temperature')->default(0)->comment('温度');
            $table->timestamp('begin_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');

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
        Schema::dropIfExists('washcar');
    }
}
