<?php

namespace App\Repositories\AcPayableContract;

use App\AccountPayableContractRecord\AccountPayableContractRecord;
use League\Flysystem\Exception;

class AcPayableContractRecordRepo
{
    function __construct(AccountPayableContractRecord $accountPayableContractRecord)
    {
        $this->model = $accountPayableContractRecord;
    }

    function create($data){
        $create = $this->model->create($data);
        return $create;
    }
}