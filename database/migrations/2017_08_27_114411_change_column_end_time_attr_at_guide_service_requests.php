<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnEndTimeAttrAtGuideServiceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guide_service_requests', function (Blueprint $table) {
			$table->string('end_time', 50)->change();
			$table->renameColumn('skill_type', 'service_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guide_service_requests', function (Blueprint $table) {
            $table->date('end_time')->change();
			$table->renameColumn('service_type', 'skill_type');
        });
    }
}
