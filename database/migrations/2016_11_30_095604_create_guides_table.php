<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('dealt_times')->default(0);
            $table->string('introducton')->nullable();
            $table->integer('following_Number')->default(0);
            $table->float('charge_per_day')->default(0);
            $table->string('currency_unit')->nullable();
            $table->json('has_guidePreService');
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
        Schema::dropIfExists('guides');
    }
}
