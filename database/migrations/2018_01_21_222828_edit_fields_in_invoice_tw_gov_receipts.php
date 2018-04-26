<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditFieldsInInvoiceTwGovReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tw_gov_receipts', function (Blueprint $table) {
            $table->text('gov_receipt_response_data')->change();
            $table->dropColumn('is_donate');
            $table->string('pay2go_status')->nullable();
            $table->string('pay2go_check_code')->nullable();
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
            $table->string('gov_receipt_response_data')->change();
            $table->boolean('is_donate')->default(0);
            $table->dropColumn(['pay2go_status','pay2go_check_code']);
        });
    }
}
