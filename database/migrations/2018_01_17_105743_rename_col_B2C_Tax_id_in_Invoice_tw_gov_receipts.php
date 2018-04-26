<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColB2CTaxIdInInvoiceTwGovReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tw_gov_receipts', function (Blueprint $table) {
            $table->renameColumn('B2C_Tax_id', 'B2B_tax_id');
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
            $table->renameColumn('B2B_tax_id', 'B2C_Tax_id');
        });
    }
}
