<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAccountTypeInMerchantSocialAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE merchant_social_accounts MODIFY account_type ENUM('fb_merchant','fb','telegram') NOT NULL");
        /*
        Schema::table('merchant_social_accounts', function (Blueprint $table) {

            $table->enum('account_type',['fb_merchant','fb','telegram'])->change();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE merchant_social_accounts MODIFY account_type ENUM('fb_merchant','fb') NOT NULL");
        /*
        Schema::table('merchant_social_accounts', function (Blueprint $table) {
            //
        });
        */
    }
}
