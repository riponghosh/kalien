<?php

namespace App\Repositories\Merchant;


use App\Merchant\AccountOperateRecord;
use App\Merchant\Merchant;
use App\Merchant\MerchantCreditAccount;
use League\Flysystem\Exception;

class MerchantRepo
{
    protected $model;
    protected $accountOperateRecord;

    function __construct(Merchant $merchant, AccountOperateRecord $accountOperateRecord)
    {
        $this->model = $merchant;
        $this->accountOperateRecord = $accountOperateRecord;
    }

    function first($merchant_id){
        $query = $this->model->where('id', $merchant_id)->first();

        return $query;
    }
    function first_by_member($merchant_id, $member_id){
        $query = $this->model->with('merchant_members')->whereHas('merchant_members',function ($q) use($member_id){
            $q->where('user_id', $member_id);
        })->find($merchant_id);

        return $query;
    }

    function get_by_member($member_id){
        $query = $this->model->with('merchant_members')->whereHas('merchant_members',function ($q) use($member_id){
            $q->where('user_id', $member_id);
        })->get();

        return $query;
    }
    function get_credit_account($merchant_id){
        $query = MerchantCreditAccount::where('merchant_id', $merchant_id)->first();

        if(!$query){
            //create new account
            $create = MerchantCreditAccount::create([
                'merchant_id' => $merchant_id,
                'credit' => 0.00,
                'currency_unit' => 'TWD',
            ]);
            if($create){
                return MerchantCreditAccount::where('merchant_id', $merchant_id)->first();
            }else{
                throw new Exception();
            }
        }

        return $query;
    }

    function update_merchant_credit($amount, $amount_unit, $method, $merchant_id){
        $merchant_account = $this->get_credit_account($merchant_id);
        if(!$merchant_account) throw new Exception();
        $merchant_credit_unit = $merchant_account->currency_unit;
        $merchant_credit = $merchant_account->credit;
        $input_amount = cur_convert($amount, $amount_unit, $merchant_credit_unit);

        if(!$input_amount) throw new Exception();

        if($method == 'increase'){
            $new_credit = $merchant_credit + $input_amount;
        }elseif($method == 'decrease'){
            $new_credit = $merchant_credit - $input_amount;
            if($new_credit < 0) throw new Exception('餘額不足已扣除款項。');
        }else{
            throw new Exception();
        }

        $update = MerchantCreditAccount::where('merchant_id', $merchant_id)->update(['credit' => $new_credit]);

        return $update;
    }
    function create_credit_account_operate_record($data){
        $query =  $this->accountOperateRecord->create($data);
        if(!$query) throw new Exception();

        return $query;
    }
}