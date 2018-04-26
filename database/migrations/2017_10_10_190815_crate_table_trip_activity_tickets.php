<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrateTableTripActivityTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_activity_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_zh_tw')->nullable();
            $table->string('description_zh_tw')->nullable();
            $table->double('amount',15,2);
            $table->string('currency_unit');
            $table->string('qty_unit')->nullable();
            $table->integer('trip_activity_id')->unsigned();
            $table->integer('merchant_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_activity_tickets');
    }
}
