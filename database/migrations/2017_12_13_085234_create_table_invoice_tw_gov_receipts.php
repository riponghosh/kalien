<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInvoiceTwGovReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_tw_gov_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->integer('gov_receipt_type')->unsigned();
            $table->integer('B2C_Tax_id')->nullable();
            $table->boolean('is_donate')->default(0);
            $table->string('gov_receipt_number');
            $table->string('mail_address')->nullable();
            $table->string('gov_receipt_response_data')->nullable();
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
        Schema::dropIfExists('invoice_tw_gov_receipts');
    }
}
