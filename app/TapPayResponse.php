<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TapPayResponse extends Model
{
    protected $connection = 'log';
    protected $table = 'tap_pay_response';
    protected $fillable = ['id', 'user_id', 'is_passed', 'tappay_record','invoice_id'];

}
?>

