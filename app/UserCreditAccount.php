<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCreditAccount extends Model
{
    use SoftDeletes;

    protected $table = 'user_credit_accounts';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

}

?>