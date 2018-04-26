<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColPdUseStartDateAtAccountPayableContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payables_contracts', function (Blueprint $table) {
            $table->dateTime('pd_use_start_date')->nullable();
            $table->dateTime('pd_use_end_date')->nullable();
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
            $table->dropColumn(['pd_use_start_date','pd_use_end_date']);
        });
    }
}
