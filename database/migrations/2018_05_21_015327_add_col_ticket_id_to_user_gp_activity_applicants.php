<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColTicketIdToUserGpActivityApplicants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_gp_activity_applicants', function (Blueprint $table) {
            $table->bigInteger('ticket_id')->nullable()->unsigned();
            $table->integer('ticket_type')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_gp_activity_applicants', function (Blueprint $table) {
            $table->dropColumn(['ticket_id', 'ticket_type']);
        });
    }
}
