<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserGpActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_gp_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('host_id')->unsigned();
            $table->string('activity_title')->nullable();
            $table->integer('activity_ticket_id');
            $table->string('start_time')->nullable();
            $table->date('start_date');
            $table->integer('duration');
            $table->string('duration_unit');
            $table->string('gp_activity_id');
            $table->tinyInteger('limit_joiner')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('user_gp_activities');
    }
}
