<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccountPayableContractRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payable_contract_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_payable_contract_id')->unsigned();
            $table->tinyInteger('role_type')->unsigned();
            $table->integer('role_id')->unsigned()->nullable();
            $table->tinyInteger('d_c')->unsigned();
            $table->float('amount',15,2)->unsigned();
            $table->string('amount_unit');
            $table->integer('action_code')->unsigned();
            $table->string('decription')->nullable();
            $table->string('note')->nullable();
            $table->string('json_info')->nullable();
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
        Schema::dropIfExists('account_payable_contract_records');
    }
}
