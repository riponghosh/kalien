<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColInvalidAtInInvoiceTwGovReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tw_gov_receipts', function (Blueprint $table) {
            $table->dateTime('invalid_at')->nullable();
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
            $table->dropColumn(['invalid_at']);
        });
    }
}
