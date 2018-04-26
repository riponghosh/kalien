<?php

namespace App\AccountPayableContract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundRule extends Model
{
    use SoftDeletes;

    protected $table = 'account_payable_contract_refund_rules';
    protected $guarded = ['id'];


}