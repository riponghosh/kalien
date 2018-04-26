<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteServicePlaces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_places', function (Blueprint $table) {
            Schema::dropIfExists('service_places');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_places', function (Blueprint $table) {
            $table->tinyInteger('id');
            $table->primary('id');
            $table->string('country_name');
            $table->string('city_name');
        });
    }
}
