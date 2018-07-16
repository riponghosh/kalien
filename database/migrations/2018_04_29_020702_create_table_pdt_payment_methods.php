<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePdtPaymentMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdt_payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ac_payable_contract_id')->unsigned();
            $table->tinyInteger('payment_type')->unsigned();
            $table->decimal('amount');
            $table->string('currency_unit');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdt_payment_methods');
    }
}
