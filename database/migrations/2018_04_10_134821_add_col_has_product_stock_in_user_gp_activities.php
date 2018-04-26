<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColHasProductStockInUserGpActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_gp_activities', function (Blueprint $table) {
            $table->boolean('has_pdt_stock')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_gp_activities', function (Blueprint $table) {
            $table->dropColumn(['has_pdt_stock']);
        });
    }
}
