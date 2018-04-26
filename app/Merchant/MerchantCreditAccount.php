<?php

namespace App\Merchant;

use Illuminate\Database\Eloquent\Model;

class MerchantCreditAccount extends Model
{
    protected $table = 'merchant_credit_accounts';

    protected $guarded = ['id'];
}