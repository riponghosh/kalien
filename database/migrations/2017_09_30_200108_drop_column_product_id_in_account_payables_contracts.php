<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnProductIdInAccountPayablesContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_payables_contracts', function (Blueprint $table) {
            $table->dropColumn(['product_id','product_type']);
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
            $table->string('product_id');
            $table->tinyInteger('product_type')->nullable();
        });
    }
}
