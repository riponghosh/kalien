<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAmountFromFloatToDecimalInAccountPayableContractRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payable_contract_records', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_payable_contract_records', function (Blueprint $table) {
            $table->float('amount', 15, 2)->change();

        });
    }
}
