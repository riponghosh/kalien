<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRestrictQtyPerDayInTripActivityTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_activity_tickets', function (Blueprint $table) {
            $table->tinyInteger('restrict_qty_per_day')->nullable();
            $table->boolean('available')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_activity_tickets', function (Blueprint $table) {
            $table->dropColumn(['restrict_qty_per_day','available']);
        });
    }
}
