<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTripActivityTicketDisableDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_activity_ticket_disable_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_ticket_id')->unsigned();
            $table->date('date');
            $table->timestamps();

            $table->foreign('activity_ticket_id')->references('id')->on('trip_activity_tickets')->onDelete('cascade');
        });
        Schema::create('trip_activity_ticket_disable_weeks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_ticket_id')->unsigned();
            $table->integer('week');
            $table->timestamps();

            $table->foreign('activity_ticket_id')->references('id')->on('trip_activity_tickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_activity_ticket_disable_dates');
        Schema::dropIfExists('trip_activity_ticket_disable_weeks');
    }
}
