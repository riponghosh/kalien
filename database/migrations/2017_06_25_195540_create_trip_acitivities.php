<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripAcitivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const ZH_TW = '_zh_tw';
    const EN = '_en';
    const JP = '_jp';

    public function up()
    {
        Schema::create('trip_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title'.self::ZH_TW)->nullable();
            $table->string('title'.self::EN)->nullable();
            $table->string('title'.self::JP)->nullable();
            $table->string('sub_title'.self::ZH_TW)->nullable();
            $table->string('sub_title'.self::EN)->nullable();
            $table->string('sub_title'.self::JP)->nullable();
            $table->string('description'.self::ZH_TW)->nullable();
            $table->string('description'.self::EN)->nullable();
            $table->string('description'.self::JP)->nullable();
            $table->string('map_address'.self::ZH_TW)->nullable();
            $table->string('map_address'.self::EN)->nullable();
            $table->string('map_address'.self::JP)->nullable();
            $table->longText('map_url')->nullable();
            $table->boolean('is_ticket')->default(0);

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
        Schema::dropIfExists('trip_activities');
    }
}
