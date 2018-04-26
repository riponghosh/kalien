<?php
namespace App\Http\Controllers;

use App\Repositories\ErrorLogRepository;
use App\Services\Transaction\Pay2GoTwGovService;
use App\Services\Transaction\TapPayService;
use App\Services\Transaction\TwGovReceiptService;
use App\Services\TransactionService;
use App\Services\TripActivityService;
use App\Services\UserGroupActivityService;
use Illuminate\Http\Request;
use App\Services\ActivityTicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityTicketController
{


    protected $activityTicketService;
    protected $tapPayService;
    protected $userGroupActivityService;
    protected $transactionService;
    protected $tripActivityService;
    protected $err_log;
    function __construct(ActivityTicketService $activityTicketService, TapPayService $tapPayService, TripActivityService $tripActivityService, UserGroupActivityService $userGroupActivityService, TransactionService $transactionService, ErrorLogRepository $errorLogRepository)
    {
        $this->activityTicketService = $activityTicketService;
        $this->tapPayService = $tapPayService;
        $this->transactionService = $transactionService;
        $this->tripActivityService = $tripActivityService;
        $this->userGroupActivityService = $userGroupActivityService;
        $this->err_log = $errorLogRepository;
    }

/*----------------------------------------------------------
*
*Activity Ticket
*
*
----------------------------------------------------------*/
//----------------------------------------------------------------------
//  票劵轉移
//----------------------------------------------------------------------
    function transfer_ticket_to_other_user(Request $request){
        if(!$request->transfer_to_uni_name || !$request->activity_ticket_id){
            return ['success' => false];
        }
        DB::beginTransaction();
        $action = $this->activityTicketService->transfer_activity_to_other_user(Auth::user()->id, $request->transfer_to_uni_name, $request->activity_ticket_id);
        if(!$action['success']){
            $msg = isset($action['msg']) ? $action['msg'] : null;
            DB::rollback();
            return ['success' => false, 'msg' => $msg];
        }
        DB::commit();
        return ['success' => true];

    }
//----------------------------------------------------------
// Incidental Coupon
//----------------------------------------------------------
    /*
     * @param
     * array incidental_coupon_ids
     */
    function use_activity_ticket_incidental_coupon(Request $request){
        DB::beginTransaction();
        $trip_activity_id = null;
        $total_amount = 0;
        if(!is_array($request->incidental_coupon_ids)) return ['success' => false];
        foreach ($request->incidental_coupon_ids as $incidental_coupon_id){
            $action = $this->activityTicketService->use_incidental_coupon_by_beneficiary($incidental_coupon_id, Auth::user()->id);
            $this->err_log->err('$incidental_coupon id: '.$incidental_coupon_id.' failed.',__CLASS__,__FUNCTION__);
            if(!$action['success']){
                $this->err_log->err('$incidental_coupon id: '.$incidental_coupon_id.' failed.',__CLASS__,__FUNCTION__);
                DB::rollback();
                return ['success' => false];
            }
            if($trip_activity_id == null){
                $trip_activity_id = $action['trip_activity_id'];
            }elseif($trip_activity_id != $action['trip_activity_id']){
                $this->err_log->err('not same activity; last trip id: '.$trip_activity_id.' ; present trip id: '.$action['trip_activity_id'],__CLASS__,__FUNCTION__);
                DB::rollback();
                return ['success' => false];
            }
            $total_amount += cur_convert($action['amount'], $action['amount_unit']);
        }

        DB::commit();

        return ['success' => true, 'total_amount' => $total_amount, 'total_amount_unit' => CLIENT_CUR_UNIT];
    }
    //----------------------------------------------------------
    // 取回Incidental Coupon
    //----------------------------------------------------------
    function retrieve_activity_ticket_incidental_coupon_by_owner(Request $request){
        if(!$request->ticket_id) return ['success' => false];
        DB::beginTransaction();
            $action = $this->activityTicketService->retrieve_activity_ticket_incidental_coupon_by_owner_id($request->ticket_id, Auth::user()->id);
            if(!$action['success']){
                $msg = isset($action['msg']) ? $action['msg'] : null;
                return ['success' => false, 'msg' => $msg];
            }
        DB::commit();

        return ['success' => true];
    }

}
?>

