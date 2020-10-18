<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->unsignedBigInteger('device_id')->index()->comment('设备编号');
            $table->unsignedBigInteger('owner_id')->index()->nullable()->comment('创建记录的人');

            $table->string('name')->nullable()->default('')->commnet('名称');
            $table->unsignedInteger('price')->nullable()->default(0)->comment('价格');
            $table->string('image')->nullable()->default('')->commnet('商品图片');
            $table->tinyInteger('is_sale')->default(true)->index()->comment('是否上架');
            $table->tinyInteger('is_recommend')->default(false)->comment('是否推荐');
            $table->unsignedInteger('seconds')->default(60)->comment('可用时长');
            $table->unsignedInteger('count')->default(1)->comment('可用次数');
            $table->unsignedInteger('days')->default(1)->commnet('有效期');

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
        Schema::dropIfExists('goods');
    }
}
