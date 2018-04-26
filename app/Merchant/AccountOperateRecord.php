<?php
namespace App\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AccountOperateRecord extends Model
{
    use SoftDeletes;
    protected $table = 'merchant_account_operate_records';
    protected $guarded = ['id'];

}
?>