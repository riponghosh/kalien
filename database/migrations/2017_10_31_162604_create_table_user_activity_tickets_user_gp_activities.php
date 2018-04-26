<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserActivityTicketsUserGpActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activity_ticket_user_gp_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_activity_ticket_id')->unsigned();
            $table->integer('user_gp_activity_id')->unsigned();
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
        Schema::dropIfExists('user_activity_ticket_user_gp_activity');
    }
}
