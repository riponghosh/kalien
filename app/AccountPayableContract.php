<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AccountPayableContract extends Model
{
    use SoftDeletes;

    protected $table = 'account_payables_contracts';
    protected $guarded = ['id'];
    protected $casts = [
        'ori_amount' => 'float'
    ];

    public function transaction_relation(){
        return $this->belongsTo('App\TransactionRelation','id','account_payable_contract_id');
    }

    public function refund_rules(){
        return $this->hasMany('App\AccountPayableContract\RefundRule', 'ac_payable_contract_id', 'id');
    }

    public function pdt_payment_methods(){
        return $this->hasMany('App\AccountPayableContract\PdtPaymentMethod', 'ac_payable_contract_id', 'id');
    }

}