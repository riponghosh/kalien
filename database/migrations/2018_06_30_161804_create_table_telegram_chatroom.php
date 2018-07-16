<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTelegramChatroom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_chatrooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('t_message_id')->nullable();
            $table->dateTime('sent_at');
            $table->string('sender_name')->nullable();
            $table->string('sender_id')->nullable();
            $table->longText('content')->nullable();
            $table->longText('raw_data')->nullable();
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
        Schema::dropIfExists('telegram_chatrooms');
    }
}
