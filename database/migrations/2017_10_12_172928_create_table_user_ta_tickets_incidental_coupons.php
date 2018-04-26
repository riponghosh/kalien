<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserTaTicketsIncidentalCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ta_tickets_incidental_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('confer_on_user_id')->unsigned()->nullable();
            $table->integer('parent_ticket_id')->unsigned();
            $table->double('amount',15,2)->nullable();
            $table->string('amount_unit')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_ticket_id')
                ->references('id')
                ->on('user_activity_tickets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ta_tickets_incidental_coupons');
    }
}
