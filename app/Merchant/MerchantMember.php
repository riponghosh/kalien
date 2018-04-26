<?php

namespace App\Merchant;

use Illuminate\Database\Eloquent\Model;

class MerchantMember extends Model
{
    protected $table = 'merchant_members';

    protected $guarded = ['id'];

    public function Merchant(){
        return $this->belongsTo('App\Merchant\Merchant', 'merchant_id', 'id');
    }
    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
?>