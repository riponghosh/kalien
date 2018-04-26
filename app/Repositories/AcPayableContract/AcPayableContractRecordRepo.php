<?php

namespace App\Repositories\AcPayableContract;

use App\AccountPayableContractRecord\AccountPayableContractRecord;

class AcPayableContractRecordRepo
{
    function __construct(AccountPayableContractRecord $accountPayableContractRecord)
    {
        $this->model = $accountPayableContractRecord;
    }

    function create($data){
        return $this->model->create($data);
    }
}