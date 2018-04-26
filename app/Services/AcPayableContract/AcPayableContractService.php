<?php

namespace App\Services\AcPayableContract;

use App\Enums\AcPayableContract\AcPayableContractRecordEnum;
use App\Repositories\AcPayableContract\AcPayableContractRecordRepo;
use App\Repositories\AcPayableContract\AcPayableContractRepo;
use League\Flysystem\Exception;
use Carbon;

class AcPayableContractService
{
    protected $repo;
    protected $record;
    const ACC_PAYABLE_AFTER_BALANCE_DATE = 2;

    function __construct(AcPayableContractRepo $acPayableContractRepo, AcPayableContractRecordRepo $acPayableContractRecordRepo)
    {
        $this->repo = $acPayableContractRepo;
        $this->record = $acPayableContractRecordRepo;
    }

    function get($attr = array()){
        return $this->repo->get($attr);
    }

    function create_and_get_id($data = array()){
        if(!isset(
            $data['user_id'],
            $data['ori_amount'],
            $data['currency_unit'],
            $data['pneko_charge_plan'],
            $data['other_fee_currency_unit'],
            $data['ori_amount'],
            $data['pd_use_start_date'],
            $data['end_date']
        )) throw new Exception('失敗。');
        if(isset($data['refund_rules'])){
            $refund_rules = $data['refund_rules'];
            unset($data['refund_rules']);
        }
        $data['pneko_fee_percentage'] = env('RATE_OF_PNEKO_FEE_FOR_MERCHANT_PLAN_'.$data['pneko_charge_plan']);
        $data['other_fee'] = env('RATE_OF_TAPPAY_FEE');
        $data['is_paid'] = false;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['settlement_time'] = date('Y-m-d',strtotime("+".self::ACC_PAYABLE_AFTER_BALANCE_DATE." days", strtotime($data['end_date'])));
        //數據整理
        unset($data['end_date']);
        unset($data['pneko_charge_plan']);
        $role_id = $data['user_id'];
        unset($data['user_id']);
        $acc_payable_id = $this->repo->create_and_get_id($data);
        if(!$acc_payable_id) throw new Exception();
        if(isset($refund_rules)){
            foreach ($refund_rules as $k => $v){
                $refund_rules[$k]['ac_payable_contract_id'] = $acc_payable_id;
            }
            $add_refund_rules = $this->repo->create_refund_rules($refund_rules);
        }
        $record = $this->record->create([
            'account_payable_contract_id' => $acc_payable_id,
            'role_type' => AcPayableContractRecordEnum::ROLE_USER,
            'role_id' => $role_id,
            'd_c' => AcPayableContractRecordEnum::CREDIT,
            'amount' => $data['ori_amount'],
            'amount_unit' => $data['currency_unit'],
            'action_code' => AcPayableContractRecordEnum::ACTION_USER_PAY,
        ]);
        if(!$record) throw new Exception();

        return $acc_payable_id;
    }
    function refund($id, $user_id, $use_refund_rule){

        $contract = $this->repo->first($id);
        if(!$contract) throw new Exception('失敗。');
        $refund_amt = $contract['ori_amount'];
        //條件1
        if($contract->is_paid != 0) throw new Exception('失敗。');
        //----------------------------------------
        //  TODO 測試
        //----------------------------------------
        if($use_refund_rule){
            if(count($contract->refund_rules) > 0){
                if(!empty($purchase_at_any_time = $contract->refund_rules->where('purchase_at_any_time', 1)->first())){
                    if($purchase_at_any_time->refund_percentage == 0){
                        throw new Exception('此票券購買成功後不能退改');
                    }
                }
                if(count($with_day_refund_rules = $contract->refund_rules->where('purchase_at_any_time', 0)) > 0){
                    $refund_rules_arr = array();
                    foreach ($with_day_refund_rules as $k => $contract_refund_rule){
                        $refund_rules_arr[$k]['refund_before_day'] = $contract_refund_rule['refund_before_day'];
                        $refund_rules_arr[$k]['refund_percentage'] = $contract_refund_rule['refund_percentage'];
                    }
                    $refund_percentage = $this->helper_refund_rule_get_percentage($refund_rules_arr, $contract->pd_use_start_date);
                    if($refund_percentage == 100 && !empty($purchase_at_any_time)){
                        $refund_percentage = $purchase_at_any_time->refund_percentage;
                    }
                    $refund_amt = $refund_amt*$refund_percentage*0.01;
                }elseif(!empty($purchase_at_any_time)){
                    $refund_amt = $refund_amt*($purchase_at_any_time->refund_percentage)*0.01;
                }
            }

        }
        if(!$this->repo->delete($id)) throw new Exception('失敗。');

        $record = $this->record->create([
            'account_payable_contract_id' => $contract->id,
            'role_type' => AcPayableContractRecordEnum::ROLE_USER,
            'role_id' => $user_id,
            'd_c' => AcPayableContractRecordEnum::DEBIT,
            'amount' => $refund_amt,
            'amount_unit' => $contract['currency_unit'],
            'action_code' => AcPayableContractRecordEnum::ACTION_REFUND,
        ]);
        if(!$record) throw new Exception('失敗。');
        //TODO return refund amt & unit
        return ['refund_amt' => $refund_amt, 'refund_amt_unit' => $contract['currency_unit']];
    }
//--------------------------------------------------------
//
//  HELPERS
//
//--------------------------------------------------------
    private function helper_refund_rule_get_percentage($arr = array(), $pd_use_start_date){
        $refund_percentage = 100;

        //--------------------------------
        // 取得距離票券使用日的天數
        //--------------------------------
        $now_day = Carbon::now();
        //設定now day 0：0：0，只diff_day計算天數
        $now_day->hour(0)->minute(0)->second(0);
        $start_day = Carbon::createFromFormat('Y-m-d H:i:s', $pd_use_start_date);
        $start_day->hour(0)->minute(0)->second(0);
        $diff_day = $now_day->diffInDays($start_day);
        //--------------------------------
        // 計算使用那一條refund rule
        //--------------------------------
        $collection = collect($arr);
        $collection = $collection->sortBy('refund_before_day')->toArray();
        foreach ($collection as $v){
            if($v['refund_before_day'] >= $diff_day){
                if($v['refund_percentage'] == 0){
                    throw new Exception('已經不能退款');
                }
                return $v['refund_percentage'];
            }
        }

        return $refund_percentage;
    }


}