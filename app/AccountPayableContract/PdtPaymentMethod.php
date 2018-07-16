<?php

namespace App\AccountPayableContract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PdtPaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'pdt_payment_methods';
    protected $guarded = ['id'];

}