<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditMediaUrlInMedias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('media_url', 'media_location_standard');
            $table->string('media_location_low')->nullable();
            $table->string('media_tag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('media_location_standard', 'media_url');
            $table->dropColumn(['media_location_low','media_tag']);
        });
    }
}
