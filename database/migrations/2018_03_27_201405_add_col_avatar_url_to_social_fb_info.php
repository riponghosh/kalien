<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColAvatarUrlToSocialFbInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_fb_info', function (Blueprint $table) {
            $table->longText('avatar_url')->nullable;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_fb_info', function (Blueprint $table) {
            $table->dropColumn(['avatar_url']);
        });
    }
}
