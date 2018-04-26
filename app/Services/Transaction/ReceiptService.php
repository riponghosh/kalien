<?php

namespace App\Services\Transaction;

use App\Repositories\ErrorLogRepository;
use App\Repositories\Transaction\ReceiptRepo;
use App\Enums\ProductTicketTypeEnum;

class ReceiptService
{
    protected $repo;
    protected $err_log;

    function __construct(ReceiptRepo $receiptRepo, ErrorLogRepository $errorLogRepository)
    {
        $this->repo = $receiptRepo;
        $this->err_log = $errorLogRepository;
    }

    function get_by_user_id($user_id){
        $data = $this->repo->get_by_user_id($user_id);
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
}