<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserGpActivityApplicants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_gp_activity_applicants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_gp_activity_id')->unsigned();
            $table->integer('applicant_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_gp_activity_id')
                ->references('id')
                ->on('user_gp_activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_gp_activity_applicants');
    }
}
