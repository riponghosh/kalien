<?php

namespace App\Repositories;

use App\Merchant\Merchant;
use App\Merchant\AccountOperateRecord;
use App\Merchant\MerchantActivityCode;
use App\Merchant\MerchantCreditAccount;
use App\Merchant\MerchantMember;

class MerchantRepository
{
    protected $merchant;
    protected $merchantMember;
    protected $err_log;
    protected $merchantActivityCode;

    function __construct(AccountOperateRecord $accountOperateRecord,Merchant $merchant, MerchantActivityCode $merchantActivityCode, MerchantMember $merchantMember, ErrorLogRepository $errorLogRepository)
    {
        $this->merchant = $merchant;
        $this->err_log = $errorLogRepository;
        $this->merchantAccountOperateRecord = $accountOperateRecord;
        $this->merchantActivityCode = $merchantActivityCode;
        $this->merchantMember = $merchantMember;
    }

    function get_merchants($cond, $num = 20){
        $query = $this->merchant;

        $query = $query->limit($num);
        $query = $query->get();
        return $query;
    }
    function get_merchant_id_by_act_code($act_code){
        return $this->merchantActivityCode->where('activity_code', $act_code)->where('disable',false)->first();
    }

    function create_merchant_member($merchant_id, $user_id){
        if($this->merchantMember->where('merchant_id', $merchant_id)->where('user_id', $user_id)->first()){
            return ['success' => false, 'msg' => '你已是此商戶成員。'];
        }
        if($this->merchantMember->create(['merchant_id' => $merchant_id, 'user_id' => $user_id])){
            return ['success' => true];
        };
    }

    function remove_act_code($act_code){
        return $this->merchantActivityCode->where('activity_code', $act_code)->update(['disable' => true]);
    }

    function get_all_merchants_by_user_id($user_id){
        $query = $this->merchantMember->with('Merchant')->where('user_id', $user_id)->get();
        return $query;
    }

    function get_merchant_by_uni_name($merchant_uni_name, $user_id, $available = true){
        $query = $this->merchant->with('merchant_members')->where('uni_name', $merchant_uni_name);
        if($available == true){
            $query = $query->whereHas('merchant_members', function ($q) use ($user_id){
                $q->where('user_id', $user_id);
            });
        }

        $query = $query->first();


        return $query;
    }

    //------------------------------------------------------
    //  Credit Account
    //------------------------------------------------------
    function update_merchant_credit($amount, $amount_unit, $method, $merchant_id, $action_code, $desc = ''){
        $merchant_record_action_codes = [
          'balance_ac_payable_contract' => 1
        ];
        $merchant_account = $this->get_merchant_account($merchant_id);
        if(!$merchant_account) return false;
        $merchant_credit_unit = $merchant_account->currency_unit;
        $merchant_credit = $merchant_account->credit;
        $input_amount = cur_convert($amount, $amount_unit, $merchant_credit_unit);

        if(!$input_amount) return false;

        if($method == 'increase'){
            $new_credit = $merchant_credit + $input_amount;
            MerchantCreditAccount::where('merchant_id', $merchant_id)->update(['credit' => $new_credit]);
        }elseif($method == 'decrease'){
            $new_credit = $merchant_credit - $input_amount;
            if($new_credit < 0) return false;
            MerchantCreditAccount::where('merchant_id', $merchant_id)->update(['credit' => $new_credit]);
        }else{
            return false;
        }
        //記録
        switch ($method){
            case 'increase':
                $d_c = 1;
                break;
            case 'decrease':
                $d_c = 2;
                break;
            default:
                $d_c = 3;
        }
        $this->merchantAccountOperateRecord->create([
            'merchant_id' => $merchant_id,
            'amount' => $amount,
            'amount_unit' => $amount_unit,
            'd_c' => $d_c,
            'action_code' => $merchant_record_action_codes[$action_code],
            'desc' => $desc
        ]);


        return true;

    }

    function get_merchant_account($merchant_id){
        $query = MerchantCreditAccount::where('merchant_id', $merchant_id)->first();
        if(!$query){
            //create new account
            $create = MerchantCreditAccount::create([
                'merchant_id' => $merchant_id,
                'credit' => 0.00,
                'currency_unit' => 'USD',
            ]);
            if($create){
                return MerchantCreditAccount::where('merchant_id', $merchant_id)->first();
            }else{
                $this->err_log->err('fail to create_user_ac :'.$merchant_id, __CLASS__, __FUNCTION__);
                return false;
            }
        }
        return $query;
    }
}
?>