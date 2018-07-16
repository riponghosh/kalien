<?php

namespace App\Services\Transaction;

use App\Enums\AcPayableContract\AcPayableContractPaymentMethodsEnum;
use App\Repositories\ErrorLogRepository;
use App\Repositories\Transaction\ReceiptRepo;
use App\Enums\ProductTicketTypeEnum;
use League\Flysystem\Exception;

class ReceiptService
{
    protected $repo;
    protected $err_log;
    public $items_payment_attr = array(); //Key is receipt_id

    function __construct(ReceiptRepo $receiptRepo, ErrorLogRepository $errorLogRepository)
    {
        $this->repo = $receiptRepo;
        $this->err_log = $errorLogRepository;
    }

    function get_by_user_id($user_id){
        $data = $this->repo->whereCond(['user_id' => $user_id])->get();
        return $data;
    }

    function delete_by_user_id($user_id){
        $delete = $this->repo->delete_by_user_id($user_id);
        if(!$delete){
            $this->err_log->err('Delete Receipt Fail', __CLASS__, __FUNCTION__);
        }
        return $delete;
    }
    function total_price($receipt){
        $total_price = 0.00;

        $receipt_gp_by = $receipt->groupBy('product_type');
        if(isset($receipt_gp_by[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET])){
            foreach ($receipt_gp_by[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET] as $k){
                if($k['trip_activity_ticket']['amount'] != null){
                    $total_price += cur_convert($k['trip_activity_ticket']['amount'],$k['trip_activity_ticket']['currency_unit']) * $k['qty'];
                }
            }
        }
        if(isset($receipt_gp_by[ProductTicketTypeEnum::USER_SERVICE_TICKET])){
            foreach($receipt_gp_by[ProductTicketTypeEnum::USER_SERVICE_TICKET] as $k){
                if($k['guide_service_ticket']['price_by_seller'] != null){
                    $total_price += cur_convert($k['guide_service_ticket']['price_by_seller'],$k['guide_service_ticket']['price_unit_by_seller']);
                }
            }
        }
        return $total_price;
    }

    function receipt_items($receipt){
        $items = array();
        $receipt_gp_by = $receipt->groupBy('product_type');
        if(isset($receipt_gp_by[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET])){
            foreach ($receipt_gp_by[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET] as $k){
                if($k['trip_activity_ticket']['amount'] != null){
                    array_push($items, [
                        'name' => $k['trip_activity_ticket']['name_zh_tw'],
                        'count' => $k['qty'],
                        'unit' => '張',
                        'price' => cur_convert($k['trip_activity_ticket']['amount'],$k['trip_activity_ticket']['currency_unit'],'TWD')
                    ]);
                }
            }
        }
        if(isset($receipt_gp_by[ProductTicketTypeEnum::USER_SERVICE_TICKET])) {
            foreach ($receipt_gp_by[ProductTicketTypeEnum::USER_SERVICE_TICKET] as $k) {
            }
        }

        return $items;
    }

/*-------------------------------------------------------------------------------------------
*
*
*   $items_payment_attr
*
*
*
------------------------------------------------------------------------------------------*/

    function get_final_payment_total_price_by_items_payment_attr(){
        $final_payment_total_price = 0.00;
        foreach ($this->items_payment_attr as $item_payment_attr) {
            $final_payment_total_price += $item_payment_attr['final_payment_amt'];
        }

        return $final_payment_total_price;
    }
//-------------------------------------------------------------------------------------------
//  結帳時，item 需要參數紀録
//  有否使用優惠券 Arr TODO 設計方向 {coupon_id, coupon_type, coupon_amt}, coupon_amt是經由邏輯處理後生產出來的coupon價值
//  Payment_method
//-------------------------------------------------------------------------------------------
    function create_items_payment_attr($receipt){
        $attr = array(
            'receipt_id' => null,
            'ori_amt' => 0.00,
            'final_payment_amt' => null,
            'coupons' => [],
            'payment_method' => [
                'pay_by_credit_card' => [
                    'amt' => null,
                    'type' => AcPayableContractPaymentMethodsEnum::CREDIT_CARD
                ],
                'pay_by_user_credit' => [
                    'amt' => null,
                    'type' => AcPayableContractPaymentMethodsEnum::USER_CREDIT
                ]
            ]
        );
        foreach ($receipt as $k => $receipts_item){
            if(empty($receipts_item->qty) || $receipts_item->qty > 10000) throw new Exception();
            for($i = 0; $i < $receipts_item->qty; $i++){
                $attr['receipt_id'] = $receipts_item->id;
                $this->items_payment_attr[(string)$receipts_item->id] = $attr;
            }
        }

        return true;
    }




    function items_init_payment_attr_ori_amt_and_final_amt($receipt){
        foreach ($receipt as $k => $receipts_item) {
            if(!isset($this->items_payment_attr[(string)$receipts_item->id])) throw new Exception();
            switch ($receipts_item['product_type']) {
                case ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET:
                    $init_amt = $receipts_item['trip_activity_ticket']['amount'];
                    break;
                case ProductTicketTypeEnum::USER_SERVICE_TICKET:
                    throw new Exception();
                    break;
                default:
                    throw new Exception();
            }
            $this->items_payment_attr[(string)$receipts_item->id]['ori_amt'] = $init_amt;
            $this->items_payment_attr[(string)$receipts_item->id]['final_payment_amt'] = $init_amt;
        }

        return true;
    }
//-------------------------------------------------------------------------------------------
//  payment_method : name, ori_amt
//  產
//
//-------------------------------------------------------------------------------------------
    function allocate_payment_method_for_items_payment_attr($payment_methods = array()){
        $total_payment_method_amt = 0.00;
        // get total payment amt
        foreach ($payment_methods as $payment_method){
            $total_payment_method_amt += $payment_method['amt'];
        }
        //set amt
        foreach ($payment_methods as $k => $payment_method){
            //calculate percentage payment_method_amt/total_payment_method_amt
            $percentage_of_total_payment_amt =  substr(sprintf("%.3f", ($payment_method['amt']/$total_payment_method_amt)),0,-1);

            foreach ($this->items_payment_attr as $key => $item_payment_attr){
                switch ($payment_method['name']){
                    case 'user_credit':
                        $pay_method_amt_key = 'pay_by_user_credit';
                        break;
                    case 'credit_card':
                        $pay_method_amt_key = 'pay_by_credit_card';
                        break;
                    default:
                        throw new Exception();
                }
                $this->items_payment_attr[$key]['payment_method'][$pay_method_amt_key]['amt'] = $percentage_of_total_payment_amt*$item_payment_attr['final_payment_amt'];
            }
        }
        return true;
    }
}