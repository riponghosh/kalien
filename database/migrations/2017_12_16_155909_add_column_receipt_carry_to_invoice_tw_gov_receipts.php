<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReceiptCarryToInvoiceTwGovReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tw_gov_receipts', function (Blueprint $table) {
            $table->tinyInteger('receipt_carry_type')->nullable()->unsigned();
            $table->string('receipt_carry_num')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_tw_gov_receipts', function (Blueprint $table) {
            $table->dropColumn(['receipt_carry_type', 'receipt_carry_num']);
        });
    }
}
