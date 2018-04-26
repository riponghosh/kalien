<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGuideServiceRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guide_service_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('request_by_id');
            $table->integer('request_to_id');
			$table->string('product_id')->nullable();
            $table->date('start_date')->nullable();
            $table->string('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->date('end_time')->nullable();
            $table->string('skill_type')->nullable();
            $table->double('price_by_seller',8,2)->nullable();
            $table->dateTime('set_expire_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guide_service_requests');
    }
}
