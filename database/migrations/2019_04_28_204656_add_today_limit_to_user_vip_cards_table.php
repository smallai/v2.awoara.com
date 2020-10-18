<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTodayLimitToUserVipCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_vip_cards', function (Blueprint $table) {
            $table->integer('today_limit')->default(3)->comment('当天可用次数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vip_cards', function (Blueprint $table) {
            $table->dropColumn('today_limit');
        });
    }
}
