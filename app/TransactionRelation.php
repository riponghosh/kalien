<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TransactionRelation extends Model
{
    use SoftDeletes;

    protected $table = 'transaction_relations';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];


    public function account_payable_contract(){
        return $this->hasOne('App\AccountPayableContract','id','account_payable_contract_id');
    }
    public function invoice_item(){
        return $this->hasOne('App\InvoiceItem','id','invoice_item_id');
    }

}
?>