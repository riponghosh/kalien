<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTripActivityTicketsFixTimeRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_activity_ticket_fix_time_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_activity_ticket_id')->unsigned();
            $table->time('start_time');
            $table->time('final_time')->nullable();
            $table->float('interval',4,1);
            $table->string('interval_unit');
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
        Schema::dropIfExists('trip_activity_ticket_fix_time_ranges');
    }
}
