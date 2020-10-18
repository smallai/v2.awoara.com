<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->increments('id');
            $table->ipAddress('ip');
            $table->string('device')->nullable()->default('')->comment('设备');
            $table->string('platform')->nullable()->default('')->comment('平台');
            $table->string('browser')->nullable()->default('')->comment('浏览器');
            $table->string('platform_version')->nullable()->default('')->comment('平台版本');
            $table->string('browser_version')->nullable()->default('')->comment('浏览器版本');
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
        Schema::dropIfExists('visitors');
    }
}
