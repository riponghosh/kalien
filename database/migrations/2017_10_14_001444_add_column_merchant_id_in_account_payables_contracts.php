<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMerchantIdInAccountPayablesContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payables_contracts', function (Blueprint $table) {
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->integer('seller_id')->unsigned()->nullable()->change();
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
            $table->dropColumn(['merchant_id']);
            $table->integer('seller_id')->unsigned()->change();
        });
    }
}
