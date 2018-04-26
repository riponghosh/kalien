<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColoumNameInChatContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_contents', function (Blueprint $table) {
            $table->renameColumn('msg_key','key');
            $table->renameColumn('sent_by_member_id','sent_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_contents', function (Blueprint $table) {
            $table->renameColumn('key','msg_key');
            $table->renameColumn('sent_by','sent_by_member_id');
        });
    }
}
