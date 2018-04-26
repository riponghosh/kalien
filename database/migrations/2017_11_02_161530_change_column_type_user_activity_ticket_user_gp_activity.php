<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeUserActivityTicketUserGpActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_activity_ticket_user_gp_activity', function (Blueprint $table) {
            $table->string('user_gp_activity_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_activity_ticket_user_gp_activity', function (Blueprint $table) {
            $table->integer('user_gp_activity_id')->unsigned()->change();
        });
    }
}
