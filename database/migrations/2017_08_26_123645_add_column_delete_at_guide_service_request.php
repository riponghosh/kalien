<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDeleteAtGuideServiceRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guide_service_requests', function (Blueprint $table) {
            $table->softDeletes();
        	$table->integer('guide_status')->default(0);
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
			$table->dropSoftDeletes();
			$table->dropColumn(['guide_status']);
        });
    }
}
