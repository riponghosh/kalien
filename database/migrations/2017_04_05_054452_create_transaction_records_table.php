<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_records_table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('buyer_id');
            $table->integer('seller_id');
            $table->string('product_id');
            $table->string('type');
            $table->float('price',8,2);
            $table->string('price_unit');
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
        Schema::dropIfExists('transaction_records_table');
    }
}
