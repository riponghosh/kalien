<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUserIdInGuideServicePlaces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guide_service_places', function (Blueprint $table) {
             $table->renameColumn('user_id', 'guide_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guide_service_places', function (Blueprint $table) {
            $table->renameColumn('guide_id', 'user_id');
        });
    }
}
