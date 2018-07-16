<?php

namespace App\Repositories\AcPayableContract;

use App\AccountPayableContract;
use App\AccountPayableContract\PdtPaymentMethod;
use App\AccountPayableContract\RefundRule;
use App\Repositories\BaseRepository;

class AcPayableContractRepo extends BaseRepository
{

    protected $refundRule;
    protected $pdtPaymentMethod;
    function __construct(RefundRule $refundRule, PdtPaymentMethod $pdtPaymentMethod)
    {
        $this->refundRule = $refundRule;
        $this->pdtPaymentMethod = $pdtPaymentMethod;

        parent::__construct();
    }

    function model(){
        return new AccountPayableContract();
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
    function create($data){
        $data['deploy_pdt_payment_method'] = true;
        return $this->model->create($data);
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

    function create_pdt_payment_methods($model, $data = array()){
        return $model->pdt_payment_methods()->createMany($data);
    }

    function get_allow_settled_contracts($attr = array()){
        $query = $this->model->with('transaction_relation');

        if(!empty($attr['balancable_from'])){
            $query = $query->whereDate('settlement_time', '>=', $attr['balancable_from']);
        }

        if(!empty($attr['balancable_to'])){
            $query = $query->whereDate('settlement_time', '<=', $attr['balancable_to']);
        }

        if(!empty($attr['merchant_ids'])){
            $query = $query->whereIn('merchant_id', $attr['merchant_ids']);
        }

        $query = $query->where('settlement_time', '<=', date('y-m-d H:i:s'))
            ->where('is_paid', false)
            ->where('complain', false)
            ->whereNotNull('merchant_id')
            ->whereNull('balanced_at');

        return $query->get();
    }
}