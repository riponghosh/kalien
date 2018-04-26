<?php

namespace App\Repositories\AcPayableContract;

use App\AccountPayableContract;
use App\AccountPayableContract\RefundRule;

class AcPayableContractRepo
{

    protected $model;
    protected $refundRule;
    function __construct(AccountPayableContract $accountPayableContract, RefundRule $refundRule)
    {
        $this->model = $accountPayableContract;
        $this->refundRule = $refundRule;
    }

    function get($attr = array()){
        $query = $this->model;
        if(isset($attr['merchant_id'])){
            $query = $query->where('merchant_id', $attr['merchant_id']);
        }

        if(isset($attr['is_balanced'])){
            $query = $query->whereNotNull('balanced_at');
        }

        if(isset($attr['query_settlement_start_date'])){
            $query = $query->whereDate('settlement_time', '>=' ,$attr['query_settlement_start_date']);
        }

        if(isset($attr['query_settlement_end_date'])){
            $query = $query->whereDate('settlement_time', '<=' ,$attr['query_settlement_end_date']);
        }

        $query = $query->get();
        return $query;
    }
    function create_and_get_id($data){
        return $this->model->insertGetId($data);
    }
    function first($id){
        return $this->model->find($id);
    }

    function delete($id){
        return $this->model->find($id)->delete();
    }

    function create_refund_rules($data){
        return $this->refundRule->insert($data);
    }
}