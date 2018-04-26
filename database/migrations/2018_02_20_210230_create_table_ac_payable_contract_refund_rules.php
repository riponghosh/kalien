<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAcPayableContractRefundRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payable_contract_refund_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('ac_payable_contract_id')->unsigned();
            $table->integer('refund_before_day')->unsigned()->nullable();
            $table->float('refund_percentage',4,1)->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_payable_contract_refund_rules');
    }
}
