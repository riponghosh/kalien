<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaTicketIncidentalCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_ticket_incidental_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_activity_ticket_id')->unsigned();
            $table->double('amount',15,2);
            $table->string('amount_unit');
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
        Schema::dropIfExists('ta_ticket_incidental_coupons');
    }
}
