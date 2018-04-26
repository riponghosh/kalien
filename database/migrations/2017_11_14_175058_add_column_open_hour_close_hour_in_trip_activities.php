<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOpenHourCloseHourInTripActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_activities', function (Blueprint $table) {
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->integer('tel_area_code')->nullable();
            $table->integer('tel')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_activities', function (Blueprint $table) {
            $table->dropColumn(['open_time','close_time']);
        });
    }
}
