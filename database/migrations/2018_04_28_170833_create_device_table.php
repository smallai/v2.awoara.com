<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->unsignedBigInteger('owner_id')->nullable()->index()->comment('设备的拥有者');

            $table->string('product_key')->nullable()->comment('阿里云的产品标识');
            $table->string('device_name')->nullable()->index()->comment('阿里云的设备名称');
            $table->string('device_secret', 512)->nullable()->comment('阿里云的设备密钥');

            $table->string('company_logo')->nullable()->comment('公司LOGO');
            $table->string('company_name')->nullable()->default('萌芽洗车')->comment('公司名称');
            $table->string('company_address')->nullable()->comment('公司地址');
            $table->string('company_phone')->nullable()->comment('公司电话');
            $table->string('company_site')->nullable()->comment('公司网站');

            $table->string('province')->nullable()->comment('省');
            $table->string('city')->nullable()->comment('市');
            $table->string('district')->nullable()->comment('区');
            $table->string('street')->nullable()->comment('街道');

            $table->string('name')->nullable()->comment('名称');
            $table->string('address')->nullable()->comment('地址');
            $table->string('phone')->nullable()->comment('联系电话');
            $table->unsignedInteger('platform_fee_rate')->nullable()->default(0)->comment('平台费费率，千分之一');
            $table->tinyInteger('is_self')->nullable()->default(false)->comment('自营');
            $table->tinyInteger('is_buyer')->nullable()->default(false)->comment('已购买');
            $table->tinyInteger('is_online')->nullable()->default(false)->commont('网络');
            $table->tinyInteger('status')->nullable()->default(\App\Models\Device::DeviceStatus_Offline)->comment('设备状态');
            $table->json('settings')->nullable()->comment('配置');

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
        Schema::dropIfExists('devices');
    }
}
