<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankCodeAtMerchantCreditAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_credit_accounts', function (Blueprint $table) {
            $table->string('bank_code',10)->nullable();
            $table->enum('bank_country',['tw'])->nullable();
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
            $table->dropColumn(['bank_code','bank_country']);
        });
    }
}
