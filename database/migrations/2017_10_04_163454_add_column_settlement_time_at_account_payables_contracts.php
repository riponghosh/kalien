<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSettlementTimeAtAccountPayablesContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payables_contracts', function (Blueprint $table) {
            $table->date('settlement_time')->nullable();
            $table->date('balanced_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_payables_contracts', function (Blueprint $table) {
            $table->dropColumn(['settlement_time','balanced_at']);
        });
    }
}
