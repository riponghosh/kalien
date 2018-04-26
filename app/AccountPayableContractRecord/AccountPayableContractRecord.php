<?php

namespace App\AccountPayableContractRecord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AccountPayableContractRecord extends Model
{
    use SoftDeletes;

    protected $table = 'account_payable_contract_records';
    protected $guarded = ['id'];

    function action_code(){
        return $this->hasOne('App\AccountPayableContractRecord\AccountPayableContractRecordActionCode', 'action_code', 'action_code');
    }
}
?>

