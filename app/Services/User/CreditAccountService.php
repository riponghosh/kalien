<?php

namespace App\Services\User;
use App\Repositories\User\CreditAccountOperationRecordRepo;
use App\Repositories\User\CreditAccountRepo;
use League\Flysystem\Exception;


class CreditAccountService
{
    protected $repo;
    protected $accountOperationRecordRepoReporationRepo;
    //Record status
    const STATUS_CREATE_AND_NOT_COMPLETE = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    function __construct(CreditAccountRepo $creditAccountRepo, CreditAccountOperationRecordRepo $accountOperationRecordRepoRepo)
    {
        $this->repo = $creditAccountRepo;
        $this->accountOperationRecordRepoReporationRepo = $accountOperationRecordRepoRepo;
    }

    function first_by_user($user_id){
        $account = $this->repo->first_by_user($user_id);

        return $account;
    }

    function increase_credit($amt, $amt_unit, $user_id, $desc = ''){
        $operation_record = $this->create_before_action_record($user_id, $user_id, $amt, $amt_unit, 1, $desc);
        $account = $this->repo->first_by_user($user_id);
        if(!$account) throw new Exception();
        $new_credit = $account->credit + cur_convert($amt, $amt_unit, $account->currency_unit);
        $update = $this->repo->update($user_id, ['credit' => $new_credit]);

        if(!$update){
            $this->update_record_to_failed($operation_record);
            throw new Exception();
        }

        $this->update_record_to_success($operation_record);

        return true;
    }

    function decrease_credit($amt, $amt_unit, $user_id, $desc = ''){
        $operation_record = $this->create_before_action_record($user_id, $user_id, $amt, $amt_unit, 2, $desc);
        $account = $this->repo->first_by_user($user_id);
        if(!$account) throw new Exception();
        if(cur_convert($account->credit, $account->currency_unit) < cur_convert($amt, $amt_unit)){
            throw new Exception('餘額不足。');
        }
        $new_credit = $account->credit - cur_convert($amt, $amt_unit, $account->currency_unit);
        $update = $this->repo->update($user_id, ['credit' => $new_credit]);

        if(!$update){
            $this->update_record_to_failed($operation_record);
            throw new Exception('扣款失敗。');
        }

        $this->update_record_to_success($operation_record);

        return true;
    }

/*-------------------------------------------------------
*
*
*  Account Operate Record
*
--------------------------------------------------------*/

//------------------------------------------------------------
//  操作前
//------------------------------------------------------------
    function create_before_action_record($operated_by, $user_id, $amt, $amt_unit, $increase_or_decrease, $desc){
        $data = [
            'operated_by' => $operated_by,
            'user_id' => $user_id,
            'amount' => $amt,
            'amount_unit' => $amt_unit,
            'status' => self::STATUS_CREATE_AND_NOT_COMPLETE,
            'increase_or_decrease' => $increase_or_decrease,
            'desc' => $desc
        ];
        return $this->accountOperationRecordRepoReporationRepo->create($data);
    }

    function update_record_to_success($record){
        return $this->accountOperationRecordRepoReporationRepo->update_by_model($record, ['status' => self::STATUS_SUCCESS]);
    }

    function update_record_to_failed($record){
        return $this->accountOperationRecordRepoReporationRepo->update_by_model($record, ['status' => self::STATUS_FAILED]);
    }
}
