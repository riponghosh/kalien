<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnHasTimeRangesAtTripActivityTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_activity_tickets', function (Blueprint $table) {
            $table->boolean('has_time_ranges')->default(false);
            $table->integer('time_range_restrict_group_num_per_day')->nullable();
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
            $table->dropColumn(['has_time_ranges', 'time_range_restrict_group_num_per_day']);
        });
    }
}
