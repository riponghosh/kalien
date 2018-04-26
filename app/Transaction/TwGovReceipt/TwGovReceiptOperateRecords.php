<?php

namespace App\Transaction\TwGovReceipt;

use Illuminate\Database\Eloquent\Model;

class TwGovReceiptOperateRecords extends Model
{
    protected $connection = 'log';
    protected $table = 'tw_gov_receipt_operate_records';

    protected $guarded = ['id'];
}