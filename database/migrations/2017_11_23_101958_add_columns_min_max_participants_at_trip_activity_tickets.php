<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsMinMaxParticipantsAtTripActivityTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_activity_tickets', function (Blueprint $table) {
            $table->integer('min_participant_for_gp_activity')->nullable();
            $table->integer('max_participant_for_gp_activity')->nullable();
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
            $table->dropColumn(['min_participant_for_gp_activity','max_participant_for_gp_activity']);
        });
    }
}
