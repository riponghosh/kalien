<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCreditFromFloatToDecimalInMerchantCreditAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_credit_accounts', function (Blueprint $table) {
            $table->decimal('credit', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_credit_accounts', function (Blueprint $table) {
            $table->float('credit', 10, 2)->change();

        });
    }
}
