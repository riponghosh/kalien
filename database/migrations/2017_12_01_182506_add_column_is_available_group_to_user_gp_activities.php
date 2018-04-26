<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsAvailableGroupToUserGpActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_gp_activities', function (Blueprint $table) {
            $table->boolean('is_available_group_for_limit_gp_ticket')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_gp_activities', function (Blueprint $table) {
            $table->dropColumn(['is_available_group_for_limit_gp_ticket']);
        });
    }
}
