<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnScheduleIdInGuideTouristMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guide_tourist_matches', function (Blueprint $table) {
            $table->string('schedule_id',255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guide_tourist_matches', function (Blueprint $table) {
            $table->tinyInteger('schedule_id')->change();
        });
    }
}
