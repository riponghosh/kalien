<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignDeleteAtUserTaTicketsIncidentalCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_ta_tickets_incidental_coupons', function (Blueprint $table) {
            $table->dropForeign(['parent_ticket_id']);
            $table->foreign('parent_ticket_id')
                ->references('id')
                ->on('user_activity_tickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_ta_tickets_incidental_coupons', function (Blueprint $table) {
            $table->dropForeign(['parent_ticket_id']);

            $table->foreign('parent_ticket_id')
                ->references('id')->on('user_activity_tickets');

        });
    }
}
