<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColInvalidByMerchantInUserGpActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_gp_activities', function (Blueprint $table) {
            $table->boolean('invalid_by_merchant')->default(false);
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
            $table->dropColumn(['invalid_by_merchant']);
        });
    }
}
