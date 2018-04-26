<?php

namespace App\Merchant;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Authenticatable
{
    protected $table = 'merchants';
    protected $hidden = ['password'];
    protected $guard = 'merchants';
    protected $guarded = ['id'];

    public function merchant_members(){
        return $this->hasMany('App\Merchant\MerchantMember', 'merchant_id', 'id');
    }

    public function merchant_credit_account(){
        return $this->hasOne('App\Merchant\MerchantCreditAccount', 'merchant_id', 'id');
    }

    public function trip_activities(){
        return $this->hasMany('App\Product','merchant_id','id');
    }
}
?>