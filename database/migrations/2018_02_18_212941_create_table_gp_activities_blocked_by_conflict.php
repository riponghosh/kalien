<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGpActivitiesBlockedByConflict extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gp_activities_blocked_for_conflict', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blocked_by')->unsigned();
            $table->integer('blocked_gp_activity_id')->unsigned();
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
        Schema::dropIfExists('gp_activities_blocked_for_conflict');
    }
}
