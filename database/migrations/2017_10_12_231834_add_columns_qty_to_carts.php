<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsQtyToCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->integer('qty')->unsigned();
            $table->date('start_date');
            $table->dropColumn(['seller_id','price', 'price_unit']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['qty','start_date']);
            $table->double('price',15,2);
            $table->string('price_unit');
            $table->integer('seller_id');
        });
    }
}
