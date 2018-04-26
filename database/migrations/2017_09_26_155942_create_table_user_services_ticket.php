<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserServicesTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_services_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_id')->unique();
            $table->integer('owner_id')->unsigned();
            $table->integer('servicer_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('service_type');
            $table->string('evblock_id');
            $table->string('evblock_name');
            $table->string('relate_schedule_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_services_tickets');
    }
}
