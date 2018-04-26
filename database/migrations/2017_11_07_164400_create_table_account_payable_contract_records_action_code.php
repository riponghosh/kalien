<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccountPayableContractRecordsActionCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payable_contract_record_action_codes', function (Blueprint $table) {
            $table->integer('action_code')->unsigned()->unique();
            $table->string('action_name');
            $table->string('action_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_payable_contract_record_action_codes');
    }
}
