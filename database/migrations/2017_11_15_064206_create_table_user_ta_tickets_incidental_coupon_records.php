<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserTaTicketsIncidentalCouponRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ta_tickets_incidental_coupon_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('processed_by_user_id')->unsigned()->nullable();
            $table->integer('incidental_coupon_id')->unsigned()->nullable();
            $table->integer('action_type')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ta_tickets_incidental_coupon_records');
    }
}
