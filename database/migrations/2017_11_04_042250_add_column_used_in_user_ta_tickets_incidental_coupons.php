<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUsedInUserTaTicketsIncidentalCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_ta_tickets_incidental_coupons', function (Blueprint $table) {
            $table->dateTime('used_at')->nullable();
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
            $table->dropColumn(['used_at']);
        });
    }
}
