<?php
namespace App\Services;

use App\EmailAPI;
use App\Enums\ProductTicketTypeEnum;
use App\Repositories\UserGroupActivity\UserGroupActivityRepo;
use App\Services\TripActivityTicket\TripActivityTicketService;
use App\UserGroupActivity\UserGroupActivity;
use Carbon\Carbon;
use League\Flysystem\Exception;

class UserGroupActivityService
{
    protected $repo;
    protected $emailAPI;
    protected $tripActivityTicketService;
    function __construct(EmailAPI $emailAPI, TripActivityTicketService $tripActivityTicketService, UserGroupActivityRepo $userGroupActivityRepo)
    {
        $this->emailAPI = $emailAPI;
        $this->repo = $userGroupActivityRepo;
        $this->tripActivityTicketService = $tripActivityTicketService;
    }
    //------------------------------------------------------
    //  Get
    //------------------------------------------------------
    /*
     * attr:
     * limit_activities 數量
     * is_not_expired (boolean)
     * {bool}is_achieved 是否限定已成團的group
     * {bool}need_min_joiner_for_avl_gp 成團需要最少購票人數
     * {bool}over_start_at 需要過期(活動開始時間)
     * query_start_date
     * query_end_date
     * not_full
     *
     *
     *
     * method:
     * allow_cancel_by_merchant  helper_allow_cancel_by_merchant
     */
    function get_group_activities($attr = array()){
        return $this->repo->get($attr); //TODO is not expired要做時區設定
    }
    function get_by_activity_ticket_id($activity_ticket_id, $attr = array(), $methods = array()){
        //判斷是一張或是多張票
        if(gettype($activity_ticket_id) == 'array'){
            $attr['activity_ticket_ids'] = $activity_ticket_id;
        }else{
            $attr['activity_ticket_id'] = $activity_ticket_id;
        }
        $gp_activities = $this->repo->get($attr);
        if(count($methods)){
            foreach ($gp_activities as $gp_activity){
                foreach ($methods as $method){
                    $gp_activity = call_user_func(array($this, 'helper_'.$method), $gp_activity);
                }

            }

        }
        return $gp_activities;
    }
    function get_all_group_activities_by_host($host_id, $attr = array()){
        $get_group_activities = $this->repo->get_by_host_id($host_id);

        if(!$get_group_activities) return ['success' => false];

        return ['success' => true, 'data' => $get_group_activities];
    }
    function get_by_participant($host_id){
        $get_group_activities = $this->repo->get_by_participant($host_id);
        return $get_group_activities;
    }

    function create_group_activity($host_id, $activity_ticket_id, $activity_title, $start_date, $start_time, $duration = null, $duration_unit = null, $limit_joiner = null){
        //需成團參數預設false
        $need_min_joiner_for_avl_gp = false;
        if($duration == null || $duration_unit == null){
            $duration_unit = null;
            $duration = null;
        }
        $get_trip_activity_ticket = $this->tripActivityTicketService->get_allow_purchase_by_id($activity_ticket_id, $start_date, $start_time);
        if(!$get_trip_activity_ticket) throw new Exception('此產品暫停售賣。');
        //活動時間長度
        if($duration == null || $duration_unit == null){
            $duration = $this->convert_duration($get_trip_activity_ticket['qty_unit'], $get_trip_activity_ticket['qty_unit_type'], 'min');
            $duration_unit = 'min';
        }
        //----------------------------------
        //檢查活動上下限人數
        //----------------------------------
        if($get_trip_activity_ticket['min_participant_for_gp_activity'] != null){
            $need_min_joiner_for_avl_gp = true;
            if(!empty($limit_joiner) && $limit_joiner < $get_trip_activity_ticket['min_participant_for_gp_activity']){
                throw new Exception('不能少於成團最小人數。');
            }
        }
        if($get_trip_activity_ticket['max_participant_for_gp_activity'] != null && $limit_joiner != null){
            if($get_trip_activity_ticket['max_participant_for_gp_activity'] < $limit_joiner){
                throw new Exception('不能超出成團最大人數。');
            }
        }
        //----------------------------------
        //  新增start_at(UTC)
        //----------------------------------
        $ticket_tz = $get_trip_activity_ticket->Trip_activity->time_zone;
        $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $start_date.' '.$start_time, $ticket_tz)->timezone('UTC');
        //---------------------------------------------------
        //  檢查是不合作商戶，不是合作商戶要啟用先團購再買票機制
        //---------------------------------------------------
        if(!empty($get_trip_activity_ticket['Trip_activity']['merchant_id']) && $get_trip_activity_ticket['Trip_activity']['merchant_id'] !=  env('MERCHANT_ID_PNEKO')){
            $has_pdt_stock = true;
        }else{
            $has_pdt_stock = false;
        }

        //新增活動
        $query_and_get_id = $this->repo->create(
            $host_id,
            $activity_ticket_id,
            $activity_title,  //host自定
            $start_date,
            $start_time,
            $start_at,
            $duration,
            $duration_unit,
            $need_min_joiner_for_avl_gp,
            $limit_joiner,
            $has_pdt_stock
        );
        if(!$query_and_get_id)throw new Exception('建立失敗。');
        //---------------------------------------------------
        //  檢查團是否能建立即Achieved
        //---------------------------------------------------
        $gp_activity = $this->repo->first_by_gp_activity_id($query_and_get_id);
        if(!$gp_activity)throw new Exception('建立失敗。');
        $gp_activity = $this->helper_gp_is_achieved($gp_activity);
        if(!$gp_activity->is_achieved){
            $gp_activity = $this->helper_check_allow_update_to_is_achieved($gp_activity);
            if($gp_activity->allow_achieved == true){
                $this->repo->update_by_id($gp_activity->id, ['is_available_group_for_limit_gp_ticket' => true, 'achieved_at' => date('Y-m-d H:i:s')]);
                $gp_activity->is_achieved = true;
            }
        }

        return ['gp_activity_id' => $gp_activity->gp_activity_id];
    }
    function get_by_gp_activity_id($activity_id, $not_full = false, $applicant_id = null, $known_is_participant = false, $allow_expired = false){
        //取團預設false,由下面檢查可否購買
        $get_activity = $this->repo->first_by_gp_activity_id($activity_id, $not_full, false);
        if(!$get_activity){
            throw new Exception('此活動不存在。');
        }
        //-------------------------------------------------------------------------
        //  已知是其中一位參加者，開放給一些需要強制購票
        //-------------------------------------------------------------------------
        if(!$known_is_participant){
            if($applicant_id != null && in_array($applicant_id , array_pluck($get_activity['applicants'],'applicant_id')) == true ){
                throw new Exception('已經是其中一位參加者，您可以先代他人購買票券再轉移。',2);
            }
        }
        $today = Carbon::today($get_activity['timezone']);
        if(!$allow_expired){
            if(strtotime($get_activity['start_date']) < strtotime($today)) {
                throw new Exception('已過了參加時間。');
            }
        }
        $get_activity = $this->helper_gp_is_achieved($get_activity);
        if(!$get_activity->is_achieved){
            $get_activity = $this->helper_check_allow_update_to_is_achieved($get_activity);
        }

        return $get_activity;

    }
    function add_participant($activity_id, $applicant_id, $group_apply_to_achieved = false, $authorizes_type = []){
        $params = [];
        if(!$gp_activity = $this->repo->first_by_gp_activity_id($activity_id, true, true)) throw new Exception('失敗，會盡快處理問題。');
        //入場券檢查
        if(!empty($gp_activity->activity_ticket_id)){ //TODO 重構增加多型，現在局限 TRIP_ACTIVITY_TICKET
            if($authorizes_type['ticket_type'] != ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET)throw new Exception('需要指定票券加入。');
            if(empty($authorizes_type['pdt_ticket_id']) || $gp_activity->activity_ticket_id != $authorizes_type['pdt_ticket_id']) throw new Exception('需要票券加入。');
            $params['ticket_type'] = $authorizes_type['ticket_type'];
            $params['ticket_id'] = $authorizes_type['ticket_id'];
        }
        $action = $this->repo->create_participant($gp_activity->id, $applicant_id, $params);
        /*
         * 重新取出新的團「已加入新成員」
         * TODO 看看有沒有方法優化，不用query兩次
         */
        $gp_activity = $this->repo->first_by_gp_activity_id($activity_id, true, true);
        //----------------------------------------------------------------------
        // 成團設定
        //----------------------------------------------------------------------
        if(empty($gp_activity->achieved_at)){
            $gp_activity = $this->helper_check_allow_update_to_is_achieved($gp_activity);
            if($gp_activity->allow_achieved == true){
                $update = $this->repo->update_by_id($gp_activity->id, ['is_available_group_for_limit_gp_ticket' => true, 'achieved_at' => date('Y-m-d H:i:s')]);
                if(!$update)throw new Exception('失敗，會盡快處理問題。');
            }
            /*
            |  Send mail
            */
            if(!empty($merchant_email = $gp_activity->trip_activity_ticket->merchant->email)){
                $this->emailAPI->group_achieved_noti_for_merchant(
                    $merchant_email,
                    $gp_activity->trip_activity_ticket->name_zh_tw,
                    $gp_activity->start_date,
                    $gp_activity->start_time,
                    $gp_activity->gp_activity_id,
                    env('DOMAIN_PATH').'/group_events/'.$gp_activity->gp_activity_id
                    );
            }

        }

        return $action;
    }
    //---------------------------------------------------------------------
    //  退團
    //---------------------------------------------------------------------
    function delete_participant_by_ticket_id($ticket_id,$gp_activity_id){
        if(!$gp_activity = $this->repo->findBy($gp_activity_id)) throw new Exception('失敗');
        //----------------------------------------------------------------------
        // 檢查人數會否小於成團下限
        //----------------------------------------------------------------------
        if($gp_activity['achieved_at'] == true){
            $min_group_joiner = $gp_activity->trip_activity_ticket->min_participant_for_gp_activity;
            if($min_group_joiner != null && count($gp_activity->applicants) - 1 < $min_group_joiner){
                $gp_activity->update(['is_available_group_for_limit_gp_ticket' => false,'achieved_at' => null]);
                if(!$gp_activity) throw new Exception('失敗，會盡快處理問題。');
            }
        }

        return $delete_applicant = $this->repo->delete_participant_by_ticket_id($ticket_id, $gp_activity_id);

    }
    function delete_participant($activity_id, $applicant_id){
        $gp_activity = $this->repo->first_by_gp_activity_id($activity_id, false, true);

        //----------------------------------------------------------------------
        // 檢查人數會否小於成團下限
        //----------------------------------------------------------------------
        if($gp_activity['achieved_at']){
            $min_group_joiner = $gp_activity->trip_activity_ticket->min_participant_for_gp_activity;
            if($min_group_joiner != null && count($gp_activity->applicants) - 1 < $min_group_joiner){
                $gp_activity->update(['is_available_group_for_limit_gp_ticket' => false]);
                if(!$gp_activity) throw new Exception('失敗，會盡快處理問題。');
            }
        }
        $delete_applicant = $this->repo->delete_participant($gp_activity, $applicant_id);

        return $delete_applicant;
    }

    //---------------------------------------------------------------------
    //  商家取消團
    //---------------------------------------------------------------------
    function delete($id){
        return $action = $this->repo->delete($id);
    }

    function get_and_delete_if_exist_user_activity_ticket_and_gp_activity_relation($activity_ticket_id){
        $res_success = ['is_activity_ticket' => false];
        $get_relation = $this->repo->get_user_activity_ticket_and_gp_activity_relation($activity_ticket_id);
        if(!$get_relation){
            return $res_success;
        }else{
            $res_success['data'] = $get_relation;
            $get_relation['user_group_activity'] = $this->helper_gp_is_achieved($get_relation['user_group_activity']);
            $res_success['is_gp_activity_ticket'] = true;
        }
        $del_relation = $this->repo->delete_user_activity_ticket_and_gp_activity_relation($get_relation['id'], 'id');
        if(!$del_relation){
            throw new Exception();
        }

        return $res_success;

    }

    function get_participant_by_ticket_id($activity_ticket_id){
        $participant = $this->repo->find_participant($activity_ticket_id, 'ticket_id');
        if(!$participant)return false;
        $participant->Group_activity = $this->helper_gp_is_achieved($participant->Group_activity);
        return $participant;

    }

    function pdt_has_stock($activity_id){
        if(!$gp_activity = $this->repo->first_by_gp_activity_id($activity_id, false, true)) throw new Exception('event is invalid');

        $gp_activity->has_pdt_stock = true;
        if(!empty($gp_activity->achieved_at)){
            $gp_activity = $this->helper_check_allow_update_to_is_achieved($gp_activity);

            if($gp_activity->allow_achieved){
                $this->repo->update_by_id($gp_activity->id, ['has_pdt_stock' => true, 'is_available_group_for_limit_gp_ticket' => true, 'achieved_at' => date('Y-m-d H:i:s')]);
            }else{
                $this->repo->update_by_id($gp_activity->id, ['has_pdt_stock' => true]);
            }
        }else{
            $gp_activity->update();
        }

        return true;
    }
    //-------------------------------------------------------------
    //  轉移參加者如果票屬某個團
    //  參加者把票券轉移時，同時也把參加者的「角色」轉移給新參加者。
    //-------------------------------------------------------------
    public function change_participant($gp_activity_id, $new_participant_id, $authorize_keys = array()){
        if(!$gp_activity = $this->repo->findBy($gp_activity_id, 'gp_activity_id')) throw new Exception('查詢無此團。');
        if(!empty($authorize = $this->get_authorize($gp_activity))){
            if($authorize['authorize_type'] == 2){
                if($authorize_keys['ticket_type'] != ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET) throw new Exception('失敗。');

                if(!$participant = $gp_activity->applicants->where('ticket_type', $authorize_keys['ticket_type'])->where('ticket_id', $authorize_keys['ticket_id'])->first()) throw new Exception('透過票權授權其他人加入失敗。');
            }
        }
        if(!$this->repo->update_participant($participant['id'], ['applicant_id' => $new_participant_id])) throw new Exception('轉移失敗。');

        return true;

    }

    function invalid_by_merchant($id){
        $gp_activity = $this->repo->first($id);
        //條件： 1.未滿團 2.是有限制的
        if(!empty($gp_activity->achieved_at)){
            throw new Exception('已成團，不能解散。');
        }
        return $this->repo->update_by_id($id, ['invalid_by_merchant' => true]);
    }

    function get_authorize($gp_activity){
        if(!empty($gp_activity['activity_ticket_id'])){
            return ['authorize_type' => 2, 'ticket_id' => $gp_activity['activity_ticket_id']];
        }
        return null;
    }
//---------------------------------------------------------------------
//  Helper
//---------------------------------------------------------------------
    function convert_duration($qty, $unit, $convert_to){
        $units = [
            'hour' => ['name' =>'hour', 'amount' => 60],
            'min' => ['name' =>'min', 'amount' => 1],
            'day' => ['name' =>'day', 'amount' => 1*60*24],
            'week' => ['name' =>'week', 'amount' => 1*60*24*7],
        ];
        if(!isset($units[$unit])) return false;
        if(!isset($units[$convert_to])) return false;

        return $qty*$units[$unit]['amount']/$units[$convert_to]['amount'];


    }
    //---------------------------------------------------------------------
    // 需成團活動能否被店家解散
    //
    //  條件1： 未成團
    //  條件2:  是否需成團活動且每天有限制售賣
    //
    //  output: $gp_activity增加allow_cancel_by_merchant
    //---------------------------------------------------------------------
    function helper_allow_cancel_by_merchant($gp_activity){
        if(empty($gp_activity->is_achieved)){
            $gp_activity->allow_cancel_by_merchant = true;
        }else{
            $gp_activity->allow_cancel_by_merchant = false;
        }
        /*
        //是否有限制團販賣
        $has_limit_sold = $gp_activity->trip_activity_ticket->time_range_restrict_group_num_per_day;
        //是否已成團
        $is_available_gp = $gp_activity->achieved_at;
        //是否有最少成團人數
        $need_min_participant = $gp_activity->need_min_joiner_for_avl_gp;
        if($has_limit_sold && !$is_available_gp && !empty($need_min_participant)){
            $gp_activity->allow_cancel_by_merchant = true;
        }else{
            $gp_activity->allow_cancel_by_merchant = false;
        }
        */

        return $gp_activity;
    }
    //---------------------------------------------------------------------
    // 是否已成團(轉譯)
    //---------------------------------------------------------------------
    function helper_gp_is_achieved($gp_activity){
        if($gp_activity->is_available_group_for_limit_gp_ticket || !empty($gp_activity->achieved_at)){
            $gp_activity->is_achieved = true;
        }else{
            $gp_activity->is_achieved = false;
        }
        return $gp_activity;

    }
    //---------------------------------------------------------------------
    // 檢查能否轉做Achieved狀態;
    // 用 achieved_at
    //
    //      2.未達人數（需搶團）
    //      4.搶團失敗
    //      5.未有存貨
    //      檢查順序要相反
    //
    //---------------------------------------------------------------------
    function get_forbidden_reason($gp_activity){
        return $this->helper_check_allow_update_to_is_achieved($gp_activity);
    }
    function helper_check_allow_update_to_is_achieved($gp_activity){
        $gp_activity->forbidden_reason = null;

        //---------------------------------
        // 是否達最少人數
        //---------------------------------
        if($gp_activity->need_min_joiner_for_avl_gp && $gp_activity->applicants->count() < $gp_activity->trip_activity_ticket->min_participant_for_gp_activity){
            $gp_activity->forbidden_reason = 2;
        //---------------------------------
        // 檢查有沒有存貨
        //---------------------------------
        }elseif($gp_activity->has_pdt_stock == false) {
            $gp_activity->forbidden_reason = 5;
        //---------------------------------
        // 檢查有沒有與「成團」的團相沖
        //---------------------------------
        }elseif(!empty($gp_activity->trip_activity_ticket['time_range_restrict_group_num_per_day'])){
            $trip_activity_tickets = $gp_activity->trip_activity_ticket->Trip_activity->trip_activity_tickets;
            $trip_activity_tickets = collect($trip_activity_tickets)->keyBy('id');

            $same_date_gp_activities = UserGroupActivity::whereIn('activity_ticket_id',$trip_activity_tickets->keys())
                ->where('start_date',$gp_activity->start_date)->whereNotNull('achieved_at')->get();
            /*檢查每團時間 TODO 考盧要否改成起始至結束時間*/
            $present_time_range = $this->helper_generate_time_ranges($gp_activity->start_time, $gp_activity->trip_activity_ticket['qty_unit'], $gp_activity->trip_activity_ticket['qty_unit_type']);
            $intersection_times = array();
            foreach ($same_date_gp_activities as $same_date_gp_activity){
                $gp_activity_ticket = $trip_activity_tickets[$same_date_gp_activity['activity_ticket_id']];
                $group_time_range = $this->helper_generate_time_ranges($same_date_gp_activity['start_time'], $gp_activity_ticket['qty_unit'], $gp_activity_ticket['qty_unit_type']);
                $get_intersection_times_from_present_and_group = $this->helper_get_two_time_range_intersection($present_time_range, $group_time_range);
                $intersection_times = array_merge_recursive($intersection_times, $get_intersection_times_from_present_and_group);
            }
            $count_intersection_times = array_count_values($intersection_times);
            foreach ($count_intersection_times as $k => $v){
                if($v >= $gp_activity->trip_activity_ticket['time_range_restrict_group_num_per_day']){
                    $gp_activity->forbidden_reason = 4;
                }
            }
        }

        if($gp_activity->forbidden_reason == null){
            $gp_activity->allow_achieved = true;
        }else{
            $gp_activity->allow_achieved = false;
        }

        $gp_activity->certified_info = $gp_activity->forbidden_reason;

        return $gp_activity;
    }
    function helper_generate_time_ranges($start_time, $qty, $interval_unit){
        $time_data = array();
        $start_time = date('H:i:s', strtotime($start_time));
        if(!in_array($interval_unit, ['hour'])) return false;
        for($i = 1; $i <= $qty; $i++){
            array_push($time_data, Carbon::createFromFormat('H:i:s', $start_time)->addHours($i - 1)->toTimeString());
        }

        return $time_data;
    }
    function helper_get_two_time_range_intersection($time_ranges_a, $time_ranges_b){
        $data = array_count_values(array_merge_recursive($time_ranges_a, $time_ranges_b));
        $output = array();
        foreach ($data as $k => $v){
            if($v > 1) array_push($output, $k);
        }
        return $output;
    }

}
?>