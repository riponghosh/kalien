<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCreditAcOperationRecord extends Model
{
    protected $connection = 'log';
    protected $table = 'user_credit_ac_operation_records';
    protected $guarded = ['id'];

}

?>