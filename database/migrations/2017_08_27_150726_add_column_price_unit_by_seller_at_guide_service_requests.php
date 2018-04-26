<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPriceUnitBySellerAtGuideServiceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guide_service_requests', function (Blueprint $table) {
            $table->string('price_unit_by_seller')->nullable();
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
            $table->dropColumn(['price_unit_by_seller']);
        });
    }
}
