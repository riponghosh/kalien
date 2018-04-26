<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbTwGovReceiptOperateRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection((string)env('DB_DATABASE_LOG'))->create('tw_gov_receipt_operate_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('tw_gov_receipt_id')->unsigned();
            $table->tinyInteger('operated_type')->unsigned();
            $table->text('pay2go_response')->nullable();
            $table->string('pay2go_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection((string)env('DB_DATABASE_LOG'))->dropIfExists('tw_gov_receipt_operate_records');
    }
}
