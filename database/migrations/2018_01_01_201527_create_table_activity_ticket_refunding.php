<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivityTicketRefunding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activity_ticket_refundings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_activity_ticket_id')->unsigned();
            $table->tinyInteger('status');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_activity_ticket_id')->references('id')->on('user_activity_tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activity_ticket_refundings');
    }
}
