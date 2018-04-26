<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccountPayables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payables_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id')->unsigned();
            $table->boolean('complain')->default(0);
            $table->double('ori_amount',15,2);
            $table->string('currency_unit');
            $table->string('product_id');
            $table->tinyInteger('product_type')->nullable();
            $table->tinyInteger('pneko_fee_percentage');
            $table->double('other_fee',15,2);
            $table->string('other_fee_currency_unit');
            $table->boolean('is_paid')->default(0);
            $table->string('description')->nullable();
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
        Schema::dropIfExists('account_payables_contracts');
    }
}
