<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTripActivityRuleInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_activity_rule_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_activity_id')->unsigned();
            $table->integer('info_id');
            $table->integer('info_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_activity_rule_infos');
    }
}
