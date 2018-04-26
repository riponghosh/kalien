<?php

namespace App\Services\UserActivityTicket;

use App\EmailAPI;
use App\Repositories\UserActivityTicket\IncidentalCouponRepo;
use App\Repositories\UserActivityTicket\UserActivityTicketRepo;
use App\Repositories\UserTicketRepository;
use App\Services\AcPayableContract\AcPayableContractService;
use App\Services\Invoice\InvoiceService;
use App\Services\Transaction\Pay2GoTwGovService;
use App\Services\Transaction\TapPayService;
use App\Services\Transaction\TwGovReceiptService;
use App\Services\TripActivityTicket\TripActivityTicketService;
use App\Services\UserGroupActivityService;
use App\TransactionRelation;
use Carbon\Carbon;
use League\Flysystem\Exception;

class ActivityTicketService
{
    protected $repo;
    protected $incidentalCouponRepo;
    protected $acPayableContractService;
    protected $emailAPI;
    protected $invoiceService;
    protected $pay2GoTwGovService;
    protected $userGroupActivityService;
    protected $tapPayService;
    protected $tripActivityTicketService;
    protected $twGovReceiptService;
    protected $userTicketRepository; //TODO 整合

    function __construct(EmailAPI $emailAPI, UserActivityTicketRepo $userActivityTicketRepo, TripActivityTicketService $tripActivityTicketService, IncidentalCouponRepo $incidentalCouponRepo, InvoiceService $invoiceService, AcPayableContractService $acPayableContractService, UserGroupActivityService $userGroupActivityService, TapPayService $tapPayService, TwGovReceiptService $twGovReceiptService, Pay2GoTwGovService $pay2GoTwGovService, UserTicketRepository $userTicketRepository)
    {
        $this->repo = $userActivityTicketRepo;
        $this->incidentalCouponRepo = $incidentalCouponRepo;
        $this->acPayableContractService = $acPayableContractService;
        $this->emailAPI = $emailAPI;
        $this->invoiceService = $invoiceService;
        $this->pay2GoTwGovService = $pay2GoTwGovService;
        $this->tapPayService = $tapPayService;
        $this->tripActivityTicketService = $tripActivityTicketService;
        $this->twGovReceiptService = $twGovReceiptService;
        $this->userGroupActivityService = $userGroupActivityService;
        $this->userTicketRepository = $userTicketRepository;
    }
    function create($data = array()){
        //檢查產品是否存在
        $trip_activity_ticket = $this->tripActivityTicketService->get_by_id($data['trip_activity_ticket_id']);
        if(!$trip_activity_ticket) throw new Exception('此產品已停售');
        $model = array(
            'owner_id' => $data['owner_id'],
            'amount' => $data['amount'],
            'currency_unit' => $data['currency_unit'],
            'trip_activity_ticket_id' => $data['trip_activity_ticket_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'name' => $data['name'],
            'sub_title' => $data['sub_title'],
            'ticket_id' =>  hash('ripemd160', $data['owner_id'].date('Y-m-d H:i:s').rand(1000,9999).rand(1000,9999))
        );


        $new_ticket =  $this->repo->create($model);

        if(!empty($trip_activity_ticket->ta_ticket_incidental_coupon)){
            $this->create_incidental_coupon(
                $new_ticket->id,
                $data['incidental_coupon_beneficiary'] ? $data['incidental_coupon_beneficiary'] : null,
                $trip_activity_ticket->ta_ticket_incidental_coupon->amount,
                $trip_activity_ticket->ta_ticket_incidental_coupon->amount_unit
            );
        }

        return $new_ticket;
    }

    function create_incidental_coupon($parent_ticket_id, $beneficiary_id = null, $amount, $amount_unit){
        return $this->incidentalCouponRepo->create([
            'parent_ticket_id' => $parent_ticket_id,
            'confer_on_user_id' => $beneficiary_id,
            'amount' => $amount,
            'amount_unit' => $amount_unit
        ]);
    }
    /*
     * [Attr]
     * owner_id
     * get_available_status
     */
    function get_all($attr = array()){
        $tickets = $this->repo->get($attr);

        foreach ($tickets as $ticket){
            if(isset($attr['get_available_status'])){
                $ticket->is_available = $this->helper_ticket_is_available($ticket);
            }
        }

        return $tickets;
    }

    function get($ticket_id, $type = 'id'){
        $ticket = $this->repo->first_by_ticket_id($ticket_id);
        if(!$ticket) throw new Exception('失敗', $ticket['status']);
        if(!$this->helper_ticket_is_available($ticket)) throw new Exception('已失效。');

        return $ticket;
    }

    function use_ticket($user_id, $ticket_id){
        $ticket = $this->get($ticket_id);
        if($ticket->owner_id != $user_id){
            throw new Exception('沒有此票券擁有權');
        }
        $this->helper_ticket_in_use_day($ticket);

        return $ticket;
    }

    function refund($user_activity_ticket){
        $use_refund_rule = true;
        //-------------------------------
        //檢查是否團體活動票
        //-------------------------------
        $get_and_delete_activity_ticket = $this->userGroupActivityService->get_and_delete_if_exist_user_activity_ticket_and_gp_activity_relation($user_activity_ticket['id']);
        if($get_and_delete_activity_ticket['is_gp_activity_ticket']){
            //-------------------------------
            //  是團購票且未成團的話不使用refund rule
            //-------------------------------
            if(!$get_and_delete_activity_ticket['data']['user_group_activity']['is_achieved']){
                $use_refund_rule = false;
            }
            $quit_if_is_gp_activity = $this->userGroupActivityService->delete_participant($get_and_delete_activity_ticket['data']['user_gp_activity_id'], $user_activity_ticket['owner_id']);
            if(!$quit_if_is_gp_activity){
                throw new Exception('失敗。');
            }
        }
        //-------------------------------
        //  TransactionRelation 連結AcPayableContract, Invoice
        //-------------------------------
        $transaction_relation = TransactionRelation::where('product_id', $user_activity_ticket['ticket_id'])->where('product_type',2)->first();
        //-------------------------------
        //  移除門票
        //-------------------------------
        $delete_ticket = $this->delete_by_model($user_activity_ticket);
        if(!$delete_ticket){throw new Exception();}
        //-------------------------------
        //  Payable Contract
        //-------------------------------
        $contract_refund = $this->acPayableContractService->refund($transaction_relation->account_payable_contract_id, $user_activity_ticket['owner_id'], $use_refund_rule);
        //-------------------------------
        //  Invoice商品移除
        //-------------------------------
        $invoice_item = $this->invoiceService->get_item_by_invoice_item_id($transaction_relation->invoice_item_id);
        $delete_invoice_item = $this->invoiceService->del_item($transaction_relation->invoice_item_id);
        //-------------------------------
        //退款
        //-------------------------------
        $create_refunding_process = false; //如果退款失敗，會新增退款處理
        $tap_pay_req_params = [
            'partner_key' => env('TAP_PAY_PARTNER_KEY'),
            'rec_trade_id' => $invoice_item->invoice['refund_id_tappay']
        ];
        $tap_pay_refund = $this->tapPayService->refund((integer)cur_convert($contract_refund['refund_amt'], $contract_refund['refund_amt_unit'],'TWD'), $tap_pay_req_params);
        if(!$tap_pay_refund['success']){
            $tap_pay_info = $tap_pay_refund['info'];
            $create_refunding_process = true;
        }
        //-------------------------------
        // 判斷是否增加退款處理
        //-------------------------------
        if($create_refunding_process){
            $this->emailAPI->refunded_fail_noti('pnekotw@gmail.com', $user_activity_ticket['id'], 'activity', date('Y-m-d H:i:s'), json_encode($tap_pay_info, true));
            $action = $this->create_ticket_refunding_process($user_activity_ticket['id'], $tap_pay_info);
        }
        //-------------------------------
        //發票作廢
        //-------------------------------
        if(empty($invoice_item->invoice['tw_gov_receipt']['id']) && empty($invoice_item->invoice['tw_gov_receipt']['gov_receipt_number'])){
            print_r('no record');
        }else{
            $invoice_invaliding = $this->pay2GoTwGovService->invoice_invalid($invoice_item->invoice['tw_gov_receipt']['gov_receipt_number'],'退票');
            $record = $this->twGovReceiptService->add_pay2go_invaliding_response_data($invoice_item->invoice['tw_gov_receipt']['id'], $invoice_invaliding);

        }

        return true;
    }
    function refund_by_owner($tic_id, $tic_owner){
        //-------------------------------
        //取得票券檢查票劵
        //-------------------------------
        $user_activity_ticket = $this->get($tic_id, 'ticket_id');
        if(!$user_activity_ticket){
            throw new Exception('失敗。');
        }
        if($user_activity_ticket->owner_id != $tic_owner){
            throw new Exception('你沒有此票的退票權限。');
        }
        //-------------------------------
        // 檢查可否退票
        // 1.在可退票的日子
        // 2.折價券未使用
        // 3.是否辦理退票中
        //
        //-------------------------------
        $this->helper_ticket_allow_refund($user_activity_ticket);
        $this->helper_ticket_in_allow_refund_duration($user_activity_ticket);
        //-------------------------------
        //  是否能退票
        //-------------------------------
        $this->refund($user_activity_ticket);

        return true;
    }
    //-----------------------------------------------
    //
    // 內部使用退票方法
    //-----------------------------------------------
    function refund_by_ticket_id($tic_id, $attr = array()){
        //-------------------------------
        //取得票券檢查票劵
        //-------------------------------
        $user_activity_ticket = $this->get($tic_id, 'ticket_id');
        if(!$user_activity_ticket){
            throw new Exception('失敗。');
        }
        //-------------------------------
        // 檢查可否退票
        // 1.折價券未使用
        // 2.是否辦理退票中
        //
        //-------------------------------
        $this->helper_ticket_allow_refund($user_activity_ticket);
        if(isset($attr['is_min_joiner_gp_and_not_avl'])){
            if(!$user_activity_ticket->relate_gp_activity->user_group_activity->is_available_group_for_limit_gp_ticket){
                throw new Exception('此票不是團票且未成團。');
            };
        }
        $this->refund($user_activity_ticket);

        return true;
    }

    function create_ticket_refunding_process($activity_ticket_uid, $desc = ""){
        $action = $this->userTicketRepository->create_ticket_refunding($activity_ticket_uid, $desc);
        if(!$action){
            return ['success' => false];
        }

        return ['success' => true];
    }

    function delete_by_model($user_activity_ticket){
        return $this->repo->delete_by_model($user_activity_ticket);
    }
//--------------------------------------------------------------
//
//      Helpers
//
//--------------------------------------------------------------
    function helper_ticket_in_use_day($user_activity_ticket){
        $tz = $user_activity_ticket['Trip_activity_ticket']['Trip_activity']['time_zone'];
        $present_time = Carbon::now()->timezone($tz);
        if(strtotime($user_activity_ticket->start_date) > strtotime($present_time->toDateString())){
            throw new Exception('',1);
        }
        if(strtotime($user_activity_ticket->start_date) < strtotime($present_time->toDateString())){
            throw new Exception('',2);
        }
        return true;
    }

    function helper_ticket_is_available($user_activity_ticket){
        $result = [ 'status' => 'available', 'msg' => []];
        //---------------------------------------------------------------------------------------
        // 日期檢查
        //---------------------------------------------------------------------------------------
        $tz = $user_activity_ticket['Trip_activity_ticket']['Trip_activity']['time_zone'];
        $present_time = Carbon::now()->timezone($tz);
        //TODO 非全日票增加start time
        $ticket_expired_at_time = $user_activity_ticket['start_date'];

        if(strtotime($present_time) > strtotime($ticket_expired_at_time.' '.'23:59')){
            $result['status'] = 'unavailable';
            array_push($result['msg'], 'expired');
        }
        //---------------------------------------------------------------------------------------
        // 團體票是否成團
        //---------------------------------------------------------------------------------------
        if(!empty($user_activity_ticket->relate_gp_activity)){
            $gp_activity = $user_activity_ticket->relate_gp_activity->user_group_activity;
            if(!$gp_activity->is_available_group_for_limit_gp_ticket){
                $result['status'] = 'unavailable';
                array_push($result['msg'], 'not_achieved');
            }
        }
        //---------------------------------------------------------------------------------------
        // Ticket Refunding TODO
        //---------------------------------------------------------------------------------------
        if(isset($user_activity_ticket['ticket_refunding'])){
            $result['status'] = 'unavailable';
            array_push($result['msg'], 'ticket_is_refunded');
        };

        return $result;
    }
    //---------------------------------------------------------------------------------------
    // 可否退款檢查
    //---------------------------------------------------------------------------------------
    function helper_ticket_allow_refund($user_activity_ticket){
        //----------------------------------------------
        //  已辦理退票
        //----------------------------------------------
        if(isset($user_activity_ticket['ticket_refunding'])){
            throw new Exception('已辦理退票。');
        };
        //----------------------------------------------
        //  檢查incidental_coupons是否有效
        //----------------------------------------------

        if($user_activity_ticket['user_ta_tickets_incidental_coupons'] != null){
            if($user_activity_ticket['user_ta_tickets_incidental_coupons']['used_at'] != null){
                throw new Exception('附屬券已被使用，不能退票。');
            }
        }
    }

    function helper_ticket_in_allow_refund_duration($user_activity_ticket){
        //預設使用日期前一天能退款
        $refund_before_day = 1;
        $tz = $user_activity_ticket['Trip_activity_ticket']['Trip_activity']['time_zone'];
        $present_DateTime = Carbon::now()->timezone($user_activity_ticket['Trip_activity_ticket']['Trip_activity']['time_zone']);
        $allow_refund_before_DateTime = Carbon::createFromFormat('Y-m-d H:i', $user_activity_ticket['start_date'].' '.'23:59', $tz);

        if(strtotime($allow_refund_before_DateTime) < strtotime($present_DateTime)){
            throw new Exception('已過了可退票時間。');
        }

        return true;

    }
}