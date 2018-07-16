<?php

namespace App\Merchant;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Merchant extends Authenticatable
{
    protected $table = 'merchants';
    protected $hidden = ['password', 'remember_token'];
    protected $guard = 'merchants';
    protected $guarded = ['id'];

    public function merchant_members(){
        return $this->hasMany('App\Merchant\MerchantMember', 'merchant_id', 'id');
    }

    public function merchant_credit_account(){
        return $this->hasOne('App\Merchant\MerchantCreditAccount', 'merchant_id', 'id');
    }

    public function trip_activities(){
        return $this->hasMany('App\Models\Product','merchant_id','id');
    }

    public function fb_merchant_ac(){
        return $this->hasOne('App\Merchant\SocialAccount','merchant_id', 'id')->where('account_type', 'fb_merchant');
    }

    public function telegram_ac(){
        return $this->hasMany('App\Merchant\SocialAccount','merchant_id', 'id')->where('account_type', 'telegram');
    }
}
?>

