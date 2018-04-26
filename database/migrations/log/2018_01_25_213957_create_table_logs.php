<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection((string)env('DB_DATABASE_LOG'))->create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('instance')->index();
            $table->string('channel')->index();
            $table->string('level')->index();
            $table->string('level_name');
            $table->text('message')->nullable();
            $table->text('context')->nullable();
            $table->text('formatted')->nullable();
            $table->text('route');
            $table->integer('remote_addr')->nullable()->unsigned();
            $table->string('user_agent')->nullable();
            $table->integer('created_by')->nullable()->index();
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
        Schema::connection((string)env('DB_DATABASE_LOG'))->dropIfExists('logs');
    }
}
