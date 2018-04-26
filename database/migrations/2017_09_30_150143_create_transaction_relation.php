<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_id')->nullable();
            $table->tinyInteger('product_type')->nullable();
            $table->integer('invoice_item_id')->unsigned()->nullable();
            $table->integer('account_payable_contract_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoice_item_id')
                ->references('id')
                ->on('invoice_items');
            $table->foreign('account_payable_contract_id')
                ->references('id')
                ->on('account_payables_contracts');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_relations');
    }
}
