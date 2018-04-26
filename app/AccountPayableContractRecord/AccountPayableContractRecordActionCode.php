<?php

namespace App\AccountPayableContractRecord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AccountPayableContractRecordActionCode extends Model
{
    use SoftDeletes;

    protected $table = 'account_payable_contract_record_action_code';
}
?>

