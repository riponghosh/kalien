<?php

namespace App\Repositories\User;

use App\UserCreditAcOperationRecord;
use League\Flysystem\Exception;

class CreditAccountOperationRecordRepo
{

    protected $model;

    function __construct(UserCreditAcOperationRecord $userCreditAcOperationRecord)
    {
        $this->model = $userCreditAcOperationRecord;
    }

    function create($data){
        $create = $this->model->create($data);
        return $create;
    }

    function update_by_model($model, $data){
        return $model->update($data);
    }
}
?>

