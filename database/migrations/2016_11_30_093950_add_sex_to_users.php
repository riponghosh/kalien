<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSexToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sex')->nullable();
            $table->string('living_address')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_tourGuide')->default(0);
            $table->string('phone_number')->nullable();
            $table->integer('age')->nullable();
            $table->json('languages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sex', 
                                'living_address',
                                'country',
                                 'is_tourGuide',
                                 'phone_number',
                                 'age',
                                 'languages',
                                ]);
        });
    }
}
