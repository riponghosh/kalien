<?php

namespace App\Services\AcPayableContract;

use App\AccountPayableContractRecord\AccountPayableContractRecord;
use App\Enums\AcPayableContract\AcPayableContractPaymentMethodsEnum;
use App\Enums\AcPayableContract\AcPayableContractRecordEnum;
use App\Enums\ProductTicketTypeEnum;
use App\Repositories\AcPayableContract\AcPayableContractRecordRepo;
use App\Repositories\AcPayableContract\AcPayableContractRepo;
use App\UserActivityTicket;
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

    /*
     * Conditions
     * settlement_start_date
     *
     *
     */
    function get($conditions = array()){
        $q_conditions = $this->make_conditions($conditions);
        return $this->repo->whereCond($q_conditions['whereConditions'])->get();
    }

    function make_conditions($conditions){
        $whereConditions = array();
        $scopedConditions = array();
        foreach ($conditions as $condition => $value){
            switch ($condition){
                case 'settlement_start_date':
                    if($value == null)break;
                    $whereConditions[] = ['settlement_time', '>=' ,$value, 'Date'];
                    break;
                case 'settlement_end_date':
                    if($value == null)break;
                    $whereConditions[] = ['settlement_time', '<=' ,$value, 'Date'];
                    break;
                case 'is_balanced':
                    if($value == true){
                        $whereConditions[] = ['balanced_at', '!=', "", 'NotNull'];
                    }elseif($value == false){
                        $whereConditions[] = ['balanced_at', '==', "", 'Null'];
                    }
                    break;
                case 'merchant_id':
                    $whereConditions['merchant_id'] = $value;
                    break;
                default:
                    break;
            }
        }

        return ['whereConditions' => $whereConditions, 'scopedConditions' => $scopedConditions];

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
        if(!isset($data['payment_methods'])) {
            throw new Exception();
        }else{
            $payment_methods = $data['payment_methods'];
            unset($data['payment_methods']);
        }

        $acc_payable = $this->repo->create($data);
        if(!$acc_payable) throw new Exception();
        if(isset($refund_rules)){
            foreach ($refund_rules as $k => $v){
                $refund_rules[$k]['ac_payable_contract_id'] = $acc_payable->id;
            }
            $add_refund_rules = $this->repo->create_refund_rules($refund_rules);
        }
        //寫入payment method
        $insert_payment_methods = array();
        foreach ($payment_methods as $payment_method){
            if(!in_array($payment_method['payment_type'], [AcPayableContractPaymentMethodsEnum::CREDIT_CARD, AcPayableContractPaymentMethodsEnum::USER_CREDIT], true)){
                throw new Exception();
            }
            $insert_payment_method = [
                'payment_type' => $payment_method['payment_type'],
                'amount' => $payment_method['amount'],
                'currency_unit' => $payment_method['amount_unit']
            ];
            $insert_payment_methods[] = $insert_payment_method;
        }
        $this->repo->create_pdt_payment_methods($acc_payable, $insert_payment_methods);
        //寫入紀録
        $record = $this->record->create([
            'account_payable_contract_id' => $acc_payable->id,
            'role_type' => AcPayableContractRecordEnum::ROLE_USER,
            'role_id' => $role_id,
            'd_c' => AcPayableContractRecordEnum::CREDIT,
            'amount' => $data['ori_amount'],
            'amount_unit' => $data['currency_unit'],
            'action_code' => AcPayableContractRecordEnum::ACTION_USER_PAY,
        ]);
        if(!$record) throw new Exception();

        return $acc_payable->id;
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
        //-------------------------------------
        //建立退款方式輸出
        //-------------------------------------
        $refund_methods_arr = array();
        //舊退款方式
        if($contract->deploy_pdt_payment_method == null){
            $refund_methods_arr[] = [
                'refund_amt' => $refund_amt,
                'refund_amt_unit' => $contract['currency_unit'],
                'refund_method_type' => AcPayableContractPaymentMethodsEnum::CREDIT_CARD
            ];
        }else{
            foreach ($contract->pdt_payment_methods as $pdt_payment_method){
                $refund_methods_arr[] = [
                    'refund_amt' => $pdt_payment_method['amount'],
                    'refund_amt_unit' => $pdt_payment_method['currency_unit'],
                    'refund_method_type' => $pdt_payment_method['payment_type']
                ];
            }
        }

        return $refund_methods_arr;
    }

    function get_only_the_product_is_used($ac_payable_contracts){
        //Get product type

        //product Trip Activity Ticket
        $type_is_trip_activity_tickets = array_where($ac_payable_contracts->toArray(),function($value, $key){
           return  $value['transaction_relation']['product_type'] == 2;
        });
        //Refactoring the product_id
        $trip_activity_ticket_ids = array_pluck($type_is_trip_activity_tickets,'transaction_relation.product_id');
        $is_used_userActivityTickets = UserActivityTicket::whereIn('ticket_id', $trip_activity_ticket_ids)->whereDate('end_date','<', Carbon::now()->timezone('Asia/Taipei')->toDateString())->get();
        $user_activity_ticket_ids = array_pluck($is_used_userActivityTickets, 'ticket_id');
        $ac_payable_contracts = $ac_payable_contracts->filter(function($value) use ($user_activity_ticket_ids){
            return in_array($value['transaction_relation']['product_id'], $user_activity_ticket_ids);
        });

        return $ac_payable_contracts;
    }

    function get_settlement_fee($ac_payable_contracts){
        return $settlement_data = $ac_payable_contracts->map(function ($ac_payable_contract){
            $pneko_fee = pneko_fee($ac_payable_contract->ori_amount,$ac_payable_contract->pneko_fee_percentage, $ac_payable_contract->currency_unit);
            $other_fee = $ac_payable_contract->ori_amount*$ac_payable_contract->other_fee*0.01;
            $merchant_revenue = $ac_payable_contract->ori_amount - $pneko_fee - $other_fee;
            $ac_payable_contract['merchant_revenue'] = $merchant_revenue;
            $ac_payable_contract['other_fee'] = $other_fee;
            $ac_payable_contract['pneko_fee'] = $pneko_fee;

            return $ac_payable_contract;
        });
    }
    function create_records($ac_payable_contracts){
        $contracts_record_data = $ac_payable_contracts->map(function ($ac_payable_contract){
            $pneko_fee = pneko_fee($ac_payable_contract->ori_amount,$ac_payable_contract->pneko_fee_percentage, $ac_payable_contract->currency_unit);
            $other_fee = $ac_payable_contract->ori_amount*$ac_payable_contract->other_fee*0.01;
            $merchant_revenue = $ac_payable_contract->ori_amount - $pneko_fee - $other_fee;
            return [
                $this->create_pneko_fee_record($ac_payable_contract->id, $pneko_fee, $ac_payable_contract->currency_unit),
                $this->create_bank_fee_record($ac_payable_contract->id, $other_fee, $ac_payable_contract->currency_unit),
                $this->create_merchant_fee_record($ac_payable_contract->id, $merchant_revenue, $ac_payable_contract->currency_unit, $ac_payable_contract->merchant_id)
            ];

        });
        $contracts_record_data = array_collapse($contracts_record_data);
        if(!AccountPayableContractRecord::insert($contracts_record_data)) throw new Exception('created records failed');
        return $contracts_record_data;
    }

    function create_pneko_fee_record($id, $fee, $currency_unit){
        return [
            'account_payable_contract_id' => $id,
            'role_type' => 3,
            'role_id' => null,
            'd_c' => 1,
            'amount' => $fee,
            'amount_unit' => $currency_unit,
            'action_code' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    function create_bank_fee_record($id, $fee, $currency_unit){
        return [
            'account_payable_contract_id' => $id,
            'role_type' => 3,
            'role_id' => null,
            'd_c' => 1,
            'amount' => $fee,
            'amount_unit' => $currency_unit,
            'action_code' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    function create_merchant_fee_record($id, $fee, $currency_unit, $merchant_id){
        return [
            'account_payable_contract_id' => $id,
            'role_type' => 2, //2 = merchant
            'role_id' => $merchant_id,
            'd_c' => 1,
            'amount' => $fee,
            'amount_unit' => $currency_unit,
            'action_code' => 5,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
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