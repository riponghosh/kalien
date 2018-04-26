<?php
namespace App\Repositories;

use App\UserCreditAcOperationRecord;
use Illuminate\Support\Facades\Auth;

class UserCreditAcOperationRecordRepository
{
    protected $operated_by;
    protected $userCreditAcOperationRecord;

    const STATUS_CREATE_AND_NOT_COMPLETE = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    function __construct(UserCreditAcOperationRecord $userCreditAcOperationRecord)
    {
        $this->userCreditAcOperationRecord = $userCreditAcOperationRecord;
    }

    function create_before_operated_record($ac_owner_id, $amount, $amount_unit, $increase_or_decrease, $desc = ''){
        $this->operated_by = isset(Auth::user()->id) ? Auth::user()->id : null;
        $data = [
            'operated_by' => $this->operated_by,
            'user_id' => $ac_owner_id,
            'amount' => $amount,
            'amount_unit' => $amount_unit,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => self::STATUS_CREATE_AND_NOT_COMPLETE,
            'increase_or_decrease' => $increase_or_decrease,
            'desc' => $desc

        ];
        $query = $this->userCreditAcOperationRecord->insertGetId($data);
        return $query;
    }

    function change_record_status_to_success($record_id){
        return $this->userCreditAcOperationRecord->where('id', $record_id)->update(['status' => self::STATUS_SUCCESS]);
    }

    function change_record_status_to_failed($record_id){
        return $this->userCreditAcOperationRecord->where('id', $record_id)->update(['status' => self::STATUS_FAILED]);
    }
}

?>