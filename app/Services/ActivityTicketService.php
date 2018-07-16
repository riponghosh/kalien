<?php
namespace App\Services;
use App\Repositories\ActivityTicketTransferRecordRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserTicketRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityTicketService
{
    protected $userService;
    protected $activityTicketTransferRecordRepository;
    protected $merchantRepository;
    protected $userGroupActivityService;
    protected $userTicketRepository;
    protected $err_log;
    protected $tripActivityService;
    function __construct(ActivityTicketTransferRecordRepository $activityTicketTransferRecordRepository, MerchantRepository $merchantRepository, UserTicketRepository $userTicketRepository, UserGroupActivityService $userGroupActivityService, TripActivityService $tripActivityService, UserService $userService, ErrorLogRepository $errorLogRepository)
    {
        $this->activityTicketTransferRecordRepository = $activityTicketTransferRecordRepository;
        $this->userService = $userService;
        $this->merchantRepository = $merchantRepository;
        $this->userGroupActivityService = $userGroupActivityService;
        $this->userTicketRepository = $userTicketRepository;
        $this->tripActivityService = $tripActivityService;
        $this->err_log = $errorLogRepository;
    }

//-----------------------------------------------------------------------
//  User Ticket 操作
//-----------------------------------------------------------------------
    function get_user_activity_ticket($user_id, $ticket_id, $type = 'id'){
        $get_ticket = $this->userTicketRepository->get_user_activity_ticket_by_ticket_id($user_id, $ticket_id, false, $type);

        if(!$get_ticket['success']){
            $this->err_log->err('ref: 1; type: '.$type.'; ticket_id: '.$ticket_id, __CLASS__, __FUNCTION__);
            return ['success' => false, 'status' => $get_ticket['status']];
        }

        return ['success' => true, 'ticket' => $get_ticket['data']];
    }
//-----------------------------------------------------------------------
//  User Incidental Ticket 操作
//-----------------------------------------------------------------------
    function use_incidental_coupon_by_beneficiary($incidental_coupon_id, $beneficiary_id){
        $action = $this->userTicketRepository->update_user_ta_tickets_incidental_coupons_used_at($incidental_coupon_id, $beneficiary_id);
        if(!$action) return ['success' => false];
        $record = $this->userTicketRepository->create_user_ta_tickets_incidental_coupons_used($incidental_coupon_id, $beneficiary_id);
        if(!$record) return ['success' => false];
        return [
            'success' => true,
            'data' => $action,
            'amount' => $action['amount'],
            'amount_unit' =>$action['amount_unit'],
            'trip_activity_id' => $action['User_activity_ticket']['Trip_activity_ticket']['Trip_activity']['id']
        ];
    }
    function retrieve_activity_ticket_incidental_coupon_by_owner_id($activity_ticket_id, $owner_id){
        $action = $this->userTicketRepository->retrieve_activity_ticket_incidental_coupon_by_owner_id($activity_ticket_id, $owner_id);
        if(!$action['success']){
            $msg = isset($action['msg']) ? $action['msg'] : null;
            return ['success' => false, 'msg' => $msg];
        }
        return ['success' => true];
    }

    function create_ticket_refunding_process($activity_ticket_uid, $desc = ""){
        $action = $this->userTicketRepository->create_ticket_refunding($activity_ticket_uid, $desc);
        if(!$action){
            return ['success' => false];
        }

        return ['success' => true];
    }
//-----------------------------------------------------------------------
//  Employee , Merchant查詢票券
//-----------------------------------------------------------------------
    /*for Employee*/
    function get_all_user_activity_tickets_by_activity_uni_name($trip_activity_ticket_uni_name){
        $cond = [
            'unexpired' => true
        ];
        $action = $this->userTicketRepository->get_all_user_activity_tickets($trip_activity_ticket_uni_name, $cond);
        if(!$action['success']) return ['success' => false];

        return ['success' => true, 'data' => $action['data']];
    }

    /*for Merchant*/
    function get_all_user_activity_tickets_by_activity_uni_name_for_merchant($trip_activity_ticket_uni_name, $merchant_mem_id){
        $cond = [
            'unexpired' => true,
            'merchant_member_id' => $merchant_mem_id
        ];
        //---------------------------------------
        //  Merchant check
        //---------------------------------------
        $action = $this->userTicketRepository->get_all_user_activity_tickets($trip_activity_ticket_uni_name, $cond);
        if(!$action['success']) return ['success' => false];

        return ['success' => true, 'data' => $action['data']];
    }
}
?>