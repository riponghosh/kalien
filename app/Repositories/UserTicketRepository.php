<?php
namespace App\Repositories;

use App\Merchant\MerchantMember;
use App\Models\Product;
use App\UserActivityTicket\TicketRefunding;
use App\Models\TripActivityTicket;
use App\UserActivityTicket;
use Carbon\Carbon;
use App\UserTaTicketsIncidentalCoupon;
use App\UserTaTicketsIncidentalCouponRecord;

class UserTicketRepository
{
    protected $merchantMember;
    protected $tripActivity;
    protected $tripActivityTicket;
    protected $userActivityTicket;
    protected $userActivityTicketRefunding;
    protected $userTaTicketsIncidentalCoupon;
    protected $userTaTicketsIncidentalCouponRecord;
    protected $err_log;
    const INCIDENTAL_COUPON_ACTION_TYPE_USED = 1;
    //Ticket Refunding
    const TICKET_REFUNDING_PROCESSING = 1;

    function __construct(MerchantMember $merchantMember, TicketRefunding $ticketRefunding, Product $tripActivity, TripActivityTicket $tripActivityTicket, UserActivityTicket $userActivityTicket, UserTaTicketsIncidentalCoupon $userTaTicketsIncidentalCoupon, UserTaTicketsIncidentalCouponRecord $userTaTicketsIncidentalCouponRecord, ErrorLogRepository $errorLogRepository)
    {
        $this->merchantMember = $merchantMember;
        $this->tripActivity = $tripActivity;
        $this->tripActivityTicket = $tripActivityTicket;
        $this->userActivityTicket = $userActivityTicket;
        $this->userActivityTicketRefunding = $ticketRefunding;
        $this->userTaTicketsIncidentalCoupon = $userTaTicketsIncidentalCoupon;
        $this->userTaTicketsIncidentalCouponRecord = $userTaTicketsIncidentalCouponRecord;
        $this->err_log = $errorLogRepository;
    }
//-------------------------------------------------------------------------------------------
//  票券
//-------------------------------------------------------------------------------------------
    function get_user_activity_tickets_by_user_id($user_id){
        return $this->userActivityTicket->where('owner_id', $user_id)->get();
    }

    function get_user_activity_ticket_by_ticket_id($user_id, $ticket_id, $in_use_day = true, $type){
        $user_activity_ticket = $this->userActivityTicket->where('owner_id', $user_id)->where($type, $ticket_id)->first();
        if(!$user_activity_ticket) return ['success' => false, 'status' => 3];
        date_default_timezone_set($tz = $user_activity_ticket['Trip_activity_ticket']['Trip_activity']['time_zone']);
        //check available
        if(!$this->helper_user_activity_ticket_available($user_activity_ticket)){
            $this->err_log->err('票券已失效',__CLASS__,__FUNCTION__);
            return ['success' => false, 'status' => 4];
        };
        //在使用日
        if($in_use_day){
            //expired
            if(strtotime($user_activity_ticket->start_date) > strtotime(date('Y-m-d'))) return ['success' => false, 'status' => 1];
            if(strtotime($user_activity_ticket->start_date) < strtotime(date('Y-m-d'))) return ['success' => false, 'status' => 2];
        }
        date_default_timezone_set("UTC");


        return ['success' => true, 'data' => $user_activity_ticket];
    }

//-------------------------------------------------------------------------------------------
//  附屬票券
//-------------------------------------------------------------------------------------------
    function get_ta_ticket_incidental_coupons_by_beneficiary_id($beneficiary_id){
        $query = $this->userTaTicketsIncidentalCoupon->with('User_activity_ticket')->where('confer_on_user_id', $beneficiary_id);
        //Condition
        $query = $query->whereHas('User_activity_ticket',function ($q){
            $q->whereDate('start_date','>=', date('y-m-d'));
        });
        $query = $query->whereNull('used_at');

        $query = $query->get();

        return $query;
    }

    function get_ta_ticket_incidental_coupons_is_used_in_n_day_by_beneficiary_id($beneficiary_id, $days){
        $query = $this->userTaTicketsIncidentalCoupon->with('User_activity_ticket')->where('confer_on_user_id', $beneficiary_id);
        //Condition TODO time zone
        $query = $query->whereDate('used_at', '<' ,Carbon::now('Asia/Taipei')->addDays($days));

        $query = $query->get();

        return $query;
    }


    function update_user_ta_tickets_incidental_coupons_used_at($incidental_coupon_id, $beneficiary_id){
        //Get
        $query = $this->userTaTicketsIncidentalCoupon->with('User_activity_ticket')->where('confer_on_user_id', $beneficiary_id)->where('id', $incidental_coupon_id);
        //Condition before query
        $query = $query->whereNull('used_at');
        $action = $query->first();
        if(!$action) return false;
        //Condition
        if($action['User_activity_ticket']['start_date'] != date('Y-m-d')) return false;
        $data = $action;
        //Update
        $update = $this->userTaTicketsIncidentalCoupon->where('id', $incidental_coupon_id)->update(['used_at' => date('Y-m-d H:i:s')]);
        if(!$update) return false;

        return $data;
    }
    //-----------------------------------
    // 取回附屬券
    //-----------------------------------
    function retrieve_activity_ticket_incidental_coupon_by_owner_id($activity_ticket_id, $owner_id){
        $activity_ticket = $this->userActivityTicket->with('user_ta_tickets_incidental_coupons')->where('owner_id', $owner_id)->where('ticket_id', $activity_ticket_id)->first();
        if(!$activity_ticket){
            return ['success' => false, 'msg' => '票券不存在'];
        }
        //----------------------------------------------
        //  檢查票券是否已使用
        //----------------------------------------------
        if($activity_ticket['user_ta_tickets_incidental_coupons']['used_at'] != null){
            return ['success' => false, 'msg' => '票券已被使用。'];
        }
        //----------------------------------------------
        //  檢查票券是否有效
        //----------------------------------------------
        if(!$this->helper_user_activity_ticket_available($activity_ticket)){
            $this->err_log->err('票券已失效', __CLASS__, __FUNCTION__);
            return ['success' => false, 'msg' => '票券已失效'];
        };
        //----------------------------------------------
        //  檢查票券是否自己持有
        //----------------------------------------------
        if($activity_ticket['user_ta_tickets_incidental_coupons']['confer_on_user_id'] == $owner_id){
            return ['success' => false, 'msg' => '你已是附屬券持有人'];
        }
        //----------------------------------------------
        //  轉換
        //----------------------------------------------
        $retrieve_ticket = $this->userTaTicketsIncidentalCoupon->where('parent_ticket_id', $activity_ticket['id'])->update(['confer_on_user_id' => $owner_id]);

        if(!$retrieve_ticket){
            $this->err_log->err('轉換失敗', __CLASS__, __FUNCTION__);
            return ['success' => false, 'msg' => '轉換失敗'];
        }
        //----------------------------------------------
        //  轉換
        //----------------------------------------------
        return ['success' => true];


    }
//-------------------------------------------------------------------------------------------
//  附屬票券記録
//-------------------------------------------------------------------------------------------
    function create_user_ta_tickets_incidental_coupons_used($incidental_coupon_id, $beneficiary_id){
        $query = $this->userTaTicketsIncidentalCouponRecord->create([
            'processed_by_user_id' => $beneficiary_id,
            'incidental_coupon_id' => $incidental_coupon_id,
            'action_type' => self::INCIDENTAL_COUPON_ACTION_TYPE_USED,
        ]);

        return $query;
    }
//-------------------------------------------------------------------------------------------
//  Ticket Refunding
//  票劵在退票中的狀態
//-------------------------------------------------------------------------------------------
    function create_ticket_refunding($user_activity_ticket_uid, $desc = ""){
        $query = $this->userActivityTicketRefunding->create([
            'user_activity_ticket_id' => $user_activity_ticket_uid,
            'desc' => $desc,
            'status' => self::TICKET_REFUNDING_PROCESSING
        ]);

        return $query;
    }

//-------------------------------------------------------------------------------------------
//  票券查詢方法
//    For: employee , merchant
//-------------------------------------------------------------------------------------------

    //-------------------------------------------------------------------------------------------
    //  取得所有票券
    //-------------------------------------------------------------------------------------------
    function get_all_user_activity_tickets($trip_activity_uni_name, $con = array(), $num = 20){
        $trip_activity = $this->tripActivity->where('uni_name', $trip_activity_uni_name)->first();
        if(!$trip_activity) return ['success' => false];
        //--------------------------------------------
        // merchant check
        //--------------------------------------------
        if(isset($con['merchant_member_id']) &&  $con['merchant_member_id'] != null){
            $check_is_activity_merchant_query = $this->merchantMember
                ->where('merchant_id', $trip_activity['merchant_id'])
                ->where('user_id', $con['merchant_member_id'])->first();

            if(!$check_is_activity_merchant_query) return ['success' => false];
        }

        $get_activity_tickets = $this->tripActivityTicket->where('trip_activity_id', $trip_activity['id'])->get();
        $activity_tickets_id = array_pluck($get_activity_tickets, 'id');

        $query = $this->userActivityTicket->whereIn('trip_activity_ticket_id', $activity_tickets_id);
        if($con['unexpired']){
            $query = $this->query_cond_user_activity_ticket_unexpired($query, $trip_activity['time_zone']);
        }
        $query = $query->limit($num);
        $query = $query->get();

        if(!$query){
            return ['success' => false];
        }

        return ['success' => true, 'data' => $query];
    }


/*---------------------------------------------------------------------------------------
**
**   HELPERS
**
---------------------------------------------------------------------------------------*/
    public function query_cond_user_activity_ticket_unexpired($query, $tz){
        $present_time = Carbon::now()->timezone($tz);
        $date_now = $present_time->toDateString();
        //$time_now = $present_time->toTimeString();
        return $query->where(function ($q) use ($date_now){
            $q->where('start_date', '>=' ,$date_now);
        });
    }
    function helper_user_activity_ticket_available($user_activity_ticket){
        //---------------------------------------------------------------------------------------
        // 日期檢查
        //---------------------------------------------------------------------------------------
        $tz = $user_activity_ticket['Trip_activity_ticket']['Trip_activity']['time_zone'];
        $present_time = Carbon::now()->timezone($tz);
        //TODO 非全日票增加start time
        $ticket_expired_at_time = $user_activity_ticket['start_date'];

        if(strtotime($present_time) > strtotime($ticket_expired_at_time.' '.'23:59')){
            return false;
        }
        //---------------------------------------------------------------------------------------
        // Ticket Refunding TODO
        //---------------------------------------------------------------------------------------
        if(isset($user_activity_ticket['ticket_refunding'])){
            return false;
        };

        return true;
    }
    //---------------------------------------------------------------------------------------
    //   For TA_ticket_incidental_coupons
    //---------------------------------------------------------------------------------------
}
?>