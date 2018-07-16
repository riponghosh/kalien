<?php

namespace App\Merchant;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $table = 'merchant_social_accounts';

    protected $guarded = ['id'];
}
