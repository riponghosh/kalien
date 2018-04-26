<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeReferenceKeyUserService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('guides', function (Blueprint $table) {
			$table->unique('user_id');
		});
        Schema::table('user_services', function (Blueprint $table) {
			$table->foreign('user_id')->references('user_id')->on('guides')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_services', function (Blueprint $table) {
			$table->dropForeign(['user_id']);
        });
		Schema::table('guides', function (Blueprint $table) {
			$table->dropUnique('guides_user_id_unique');
		});
    }
}
