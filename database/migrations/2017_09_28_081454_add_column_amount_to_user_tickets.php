<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAmountToUserTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_services_tickets', function (Blueprint $table) {
            $table->double('amount',15,2)->nullable();
            $table->string('currency_unit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_services_tickets', function (Blueprint $table) {
            $table->dropColumn(['amount','currency_unit']);
        });
    }
}
