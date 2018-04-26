<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColPurchaseAtAnyTimeInTripActivityRefundRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_activity_refund_rules', function (Blueprint $table) {
            $table->boolean('purchase_at_any_time')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_activity_refund_rules', function (Blueprint $table) {
            $table->dropColumn(['purchase_at_any_time']);
        });
    }
}
