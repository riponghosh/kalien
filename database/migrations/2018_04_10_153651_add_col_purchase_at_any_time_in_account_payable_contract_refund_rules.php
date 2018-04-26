<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColPurchaseAtAnyTimeInAccountPayableContractRefundRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payable_contract_refund_rules', function (Blueprint $table) {
            $table->boolean('purchase_at_any_time')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_payable_contract_refund_rules', function (Blueprint $table) {
            $table->dropColumn(['purchase_at_any_time']);
        });
    }
}
