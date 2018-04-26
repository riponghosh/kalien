<?php

namespace App\Services;


use App\Enums\Merchant\MerchantEnum;
use App\Repositories\ErrorLogRepository;
use App\Repositories\Merchant\MerchantRepo;
use App\Repositories\MerchantRepository;
use App\Repositories\TransactionRepository;

class MerchantService
{
    protected $repo;
    protected $merchantRepository;
    protected $transactionRepository;
    protected $err_log;
    function __construct(MerchantRepo $merchantRepo, MerchantRepository $merchantRepository, TransactionRepository $transactionRepository, ErrorLogRepository $errorLogRepository)
    {
        $this->repo = $merchantRepo;
        $this->merchantRepository = $merchantRepository;
        $this->transactionRepository = $transactionRepository;
        $this->err_log = $errorLogRepository;
    }

    public function first($merchant_id){
        return $this->repo->first($merchant_id);
    }
    public function get_by_member($merchant_id, $member_id){
        return $this->repo->first_by_member($merchant_id, $member_id);
    }

    public function get_all_by_member($member_id){
        return $this->repo->get_by_member($member_id);
    }
    public function create_merchant_member_by_act_code($user_id, $act_code){
        $query1 = $this->merchantRepository->get_merchant_id_by_act_code($act_code);
        if(!$query1){
            $this->err_log->err('ref_code: 1', __CLASS__, __FUNCTION__);
            return ['success' => false];
        }
        $query2 = $this->merchantRepository->create_merchant_member($query1->merchant_id, $user_id);
        if(!$query2['success']){
            $this->err_log->err('ref_code: 2', __CLASS__, __FUNCTION__);
            return ['success' => false, 'msg' => $query2['msg']];
        }
        $query3 = $this->merchantRepository->remove_act_code($act_code);
        if(!$query3) {
            $this->err_log->err('ref_code: 3', __CLASS__, __FUNCTION__);
            return ['success' => false];
        }

        return ['success' => true];
    }

    public function get_all_merchants_by_employee($cond = array()){
        return $this->merchantRepository->get_merchants($cond);
    }
    public function get_all_merchants_by_user_id($user_id){
        $query = $this->merchantRepository->get_all_merchants_by_user_id($user_id);
        if(!$query) return ['success' => false];
        return ['success' => true, 'data' => $query];
    }

    public function get_merchant_by_id($merchant_uni_name, $user_id){
        $query = $this->merchantRepository->get_merchant_by_uni_name($merchant_uni_name, $user_id);
        if(!$query) return ['success' => false];
        return ['success' => true, 'data' => $query];
    }

    //-------------------------------------------------------------
    // Employee 用
    //-------------------------------------------------------------
    public function get_merchant_info($merchant_uni_name){
        $query = $this->merchantRepository->get_merchant_by_uni_name($merchant_uni_name, null, false);
        if(!$query) return ['success' => false];
        return ['success' => true, 'data' => $query];
    }

    public function get_merchant_payable_contracts($merchant_id, $attrs = array()){
        $query = $this->transactionRepository->get_merchant_ac_payable_contracts_record(
            $merchant_id,
            $attrs['settlement_start_date'],
            $attrs['settlement_end_date']
        );
        if(!$query) return ['success' => false];
        return ['success' => true, 'data' => $query];
    }

    public function account_withdrawal($merchant_id, $amount, $amount_unit, $desc = ''){
        $update_account = $this->repo->update_merchant_credit($amount, $amount_unit, 'decrease', $merchant_id);
        $record = $this->repo->create_credit_account_operate_record([
            'merchant_id' => $merchant_id,
            'amount' => $amount,
            'amount_unit' => $amount_unit,
            'd_c' => 2,
            'action_code' => MerchantEnum::ACCOUNT_OPERATE_MERCHANT_WITHDRAWAL,
            'desc' => $desc
        ]);

        return $update_account;

    }
}
?>