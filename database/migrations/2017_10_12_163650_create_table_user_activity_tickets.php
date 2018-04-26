<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserActivityTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activity_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_id');
            $table->integer('trip_activity_ticket_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('sub_title')->nullable();
            $table->integer('owner_id')->unsigned();
            $table->date('start_date');
            $table->date('end_date');
            $table->double('amount',15,2)->nullable();
            $table->string('currency_unit')->nullable();
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
        Schema::dropIfExists('user_activity_tickets');
    }
}
