<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripActivityMedia extends Migration
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
        Schema::create('trip_activity_media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_activity_id');
            $table->integer('media_id');
            $table->string('description'.self::ZH_TW)->nullable();
            $table->string('description'.self::EN)->nullable();
            $table->string('description'.self::JP)->nullable();
            $table->boolean('is_gallery_image')->default(0);
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
        Schema::dropIfExists('trip_activity_media');
    }
}
