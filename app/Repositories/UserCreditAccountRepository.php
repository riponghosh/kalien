<?php

namespace App\Repositories;
use App\UserCreditAccount;

class UserCreditAccountRepository
{
    protected $userCreditAccount;
    protected $err_log;
    const CLASS_NAME = __CLASS__;

    function __construct(UserCreditAccount $userCreditAccount, ErrorLogRepository $errorLogRepository)
    {
        $this->userCreditAccount = $userCreditAccount;
        $this->err_log = $errorLogRepository;
    }

    function update_user_credit($amount, $amount_unit, $method, $user_id){
        $user_account = $this->get_user_account($user_id);
        $original_credit = $user_account->credit;
        $original_credit_unit = $user_account->currency_unit;
        if(!$user_account) return false;
        $user_credit_unit = $user_account->currency_unit;
        $user_credit = $user_account->credit;
        $input_amount = cur_convert($amount, $amount_unit, $user_credit_unit);

        if(!$input_amount) return ['success' => false,'msg' => '幣值轉換失敗'];
        if($method == 'increase'){
            $new_credit = $user_credit + $input_amount;
            UserCreditAccount::where('user_id', $user_id)->update(['credit' => $new_credit]);
        }elseif($method == 'decrease'){
            $new_credit = $user_credit - $input_amount;
            if($new_credit < 0) return ['success' =>false];
            UserCreditAccount::where('user_id', $user_id)->update(['credit' => $new_credit]);
        }else{
            return ['success' => false, 'msg' => 'decrease 功能失敗'];
        }

        return ['success' => true, 'original_credit' => $original_credit, 'original_credit_unit' => $original_credit_unit];
    }

    function get_user_account($user_id){
        $query = UserCreditAccount::where('user_id', $user_id)->first();
        if(!$query){
            //create new account
            $create = UserCreditAccount::create([
                'user_id' => $user_id,
                'credit' => 0.00,
                'currency_unit' => 'TWD',
            ]);
            if($create){
                return UserCreditAccount::where('user_id', $user_id)->first();
            }else{
                $this->err_log->err('fail to create_user_ac :'.$user_id, self::CLASS_NAME, __FUNCTION__);
                return false;
            }
        }
        return $query;
    }
}
?>