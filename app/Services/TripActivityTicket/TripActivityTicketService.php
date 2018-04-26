<?php

namespace App\Services\TripActivityTicket;

use App\Repositories\TripActivity\TripActivityRepo;
use App\Repositories\TripActivityTicket\TripActivityTicketRepo;
use App\TripActivityTicket;
use App\UserGroupActivity\UserGroupActivity;
use League\Flysystem\Exception;
use Carbon\Carbon;

class TripActivityTicketService
{
    protected $repo;
    protected $tripActivityRepo;
    function __construct(TripActivityTicketRepo $tripActivityTicketRepo, TripActivityRepo $tripActivityRepo)
    {
        $this->tripActivityRepo = $tripActivityRepo;
        $this->repo = $tripActivityTicketRepo;
    }

    function get_by_id($id){
        $ticket = $this->repo->first($id);
        $ticket = $this->helper_is_available($ticket);

        return $ticket;
    }
    function get_by_ids($ids, $attr = array()){
        $attr['ids'] = $ids;
        $tickets = $this->repo->get($attr);

        foreach ($tickets as $ticket){
            $ticket = $this->helper_is_available($ticket);
        }

        return $tickets;

    }
    //------------------------------------------------------------------
    // 透過activity 取得 all package
    //------------------------------------------------------------------
    function get_all_by_trip_activity($attr = array()){
        $tickets = $this->repo->get_by_trip_activity($attr);

        foreach ($tickets as $ticket){
            $ticket = $this->helper_is_available($ticket);
        }
        return $tickets;
    }
    function get_all_by_trip_activity_uni_name($trip_activity_uni_name){
        $trip_activity = $this->tripActivityRepo->first_by_uni_name($trip_activity_uni_name);
        if(!$trip_activity) throw new Exception();
        $tickets = $this->repo->get_by_trip_activity_id($trip_activity->id);
        foreach ($tickets as $k => $ticket){
            try{
                $this->helper_is_available($ticket);
            }catch (Exception $e){
                unset($tickets[$k]);
            }
        }

        return $tickets;

    }

    function get_allow_purchase_by_id($id, $start_date, $start_time = null, $attr = array()){
        $ticket = $this->repo->first($id);
        $ticket = $this->helper_is_available($ticket);
        $ticket = $this->helper_is_allow_purchase($ticket, $start_date, $start_time, $attr);

        return $ticket;
    }

    function get_blacklist_dates($trip_activity_ticket){
        return $this->helper_get_ticket_blacklist_dates($trip_activity_ticket);
    }

    function get_start_time_ranges($trip_activity_ticket){
        if($trip_activity_ticket['has_time_ranges']){
            $time_ranges = $this->helper_create_activity_tickets_start_times_for_fix_time_ranges($trip_activity_ticket);
        }else{
            $time_ranges = $this->helper_create_activity_tickets_start_times(
                $trip_activity_ticket['Trip_activity']['open_time'],
                $trip_activity_ticket['Trip_activity']['close_time'],
                $trip_activity_ticket['qty_unit'],
                $trip_activity_ticket['qty_unit_type']
            );
        }

        return  $time_ranges;
    }
//------------------------------------------------------------------------
//
//   Helpers
//
//------------------------------------------------------------------------

    //--------------------------------------------------------
    //  門票是可以發售
    //--------------------------------------------------------
    function helper_is_available($trip_activity_ticket){
        if($trip_activity_ticket['Trip_activity']['merchant_id'] == null || $trip_activity_ticket['available'] == false){
            throw new Exception('活動不存在或已失效。');
        }

        return $trip_activity_ticket;
    }
    //--------------------------------------------------------
    //  門票是可購買，
    //  例如是否已售完；當天場次是否開賣等
    //--------------------------------------------------------
    function helper_is_allow_purchase($trip_activity_ticket,  $start_date, $start_time = null, $attr = array()){
        //----------------------------------------
        //  #1 預設disable 售賣日子
        //----------------------------------------
        if(!$this->helper_is_in_available_date($trip_activity_ticket, $start_date)){
            throw new Exception('此時刻的門票已售完。');
        };
        //----------------------------------------
        //  #2 Time range
        //  如有 $attr['is_available_group_for_limit_gp_ticket']，代表此團是已成團，加入此團無需檢查是否有時間重疊問題。
        //----------------------------------------
        if(!$trip_activity_ticket->has_time_ranges == 0 && !isset($attr['is_available_group_for_limit_gp_ticket'])){
            if($start_time == null){
                throw new Exception('請提供場次。');
            }else{
                if(!$this->helper_ticket_time_range_available_for_purchase($trip_activity_ticket, $start_date, $start_time)){
                    throw new Exception('場次已滿');
                };
            }
        }
        //---------------------------------------
        // #3購買日期 票券必在n天前停止購買 TODO 寫入trip_activity_ticket 邏輯，每種票券都有不同限時
        // Start_date(using date) > $present_date - n天前
        //---------------------------------------
        $purchase_before_day = 0;
        //now
        $present_time = Carbon::now()->timezone($trip_activity_ticket['Trip_activity']['time_zone']);
        //能票券使用日
        $start_time_for_now = $start_time != null ? date('H:i:s', strtotime($start_time)) : '23:59:00';   //補償時間：如果時沒有指定使用時間，就會是例2017-11-11 23：59，即可購買2017-11-11 22:00的票
        $purchase_before_date = Carbon::createFromFormat('Y-m-d H:i:s', $start_date.' '.$start_time_for_now);
        $purchase_before_date->subDays($purchase_before_day);
        if(strtotime(date($present_time)) > strtotime($purchase_before_date)){
            throw new Exception('已逾期。');
        }
        //---------------------------------------
        // TODO 開放有限票券檢查
        //---------------------------------------
        return $trip_activity_ticket;
    }
    //--------------------------------------------------------
    //  日子黑名單檢查
    //--------------------------------------------------------
    function helper_is_in_available_date($trip_activity_ticket, $start_date){
        $disable_dates = $this->helper_get_ticket_blacklist_dates($trip_activity_ticket);
        $durations = [$disable_dates['start_date'], $disable_dates['end_date']];
        //星期檢查
        if(count($disable_dates['disable_weeks']) > 0){
            foreach ($disable_dates['disable_weeks'] as $week){
                $dates_of_week = get_durations_date_of_special_week($durations[0], $durations[1], $week);
                if(in_array($start_date, $dates_of_week)) return false;
            }
        }
        //日子檢查
        if(in_array($start_date, $disable_dates['disable_dates'])) return false;

        return $trip_activity_ticket;
    }
    //--------------------------------------------------------
    //  日子黑名單Formatter
    //  已陣列輸出: dates ['2017-01-01','2017-01-03']
    //--------------------------------------------------------
    function helper_get_ticket_blacklist_dates($trip_activity_ticket){
        return [
            'disable_weeks' => array_pluck($trip_activity_ticket->disable_weeks,'week'),
            'disable_dates' => array_pluck($trip_activity_ticket->disable_dates,'date'),
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(35)->toDateString()
        ];
    }
    //------------------------------------------------------------------
    //      Time Range function
    //
    //------------------------------------------------------------------
    function helper_ticket_time_range_available_for_purchase($trip_activity_ticket, $start_date, $start_time){
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
        //----------------------------------------------------
        //  檢查是否在營業時間 且格式是否標準
        //----------------------------------------------------
        if(!$this->helper_time_in_fix_time_ranges($trip_activity_ticket, $start_time)){
            throw new Exception('沒有此場次。');
        }
        if (empty($trip_activity_ticket['time_range_restrict_group_num_per_day'])) return true;
        //----------------------------------------------------
        //條件： (1)所有GP_activity {trip_activity_ticket_id, 'start_date', 'has_time_range'}
        //      (2)已成團
        //----------------------------------------------------
        /*取出所有行程的票券*/
        $all_trip_activity_tickets = TripActivityTicket::where('trip_activity_id', $trip_activity_ticket['trip_activity_id'])->get();
        $all_trip_activity_tickets_id = array_pluck($all_trip_activity_tickets, 'id');

        $all_group_activities = UserGroupActivity::with('applicants')
            ->whereIn('activity_ticket_id', $all_trip_activity_tickets_id)
            ->where('start_date', $start_date);
        /*需有成團認證*/
        $all_group_activities = $all_group_activities->where('is_available_group_for_limit_gp_ticket', true);
        $all_group_activities = $all_group_activities->get();
        /*刪除人數不足團*/
        if($trip_activity_ticket['min_participant_for_gp_activity'] != null){
            foreach ($all_group_activities as $k => $v){
                if($v->applicants->count() < $trip_activity_ticket['min_participant_for_gp_activity']){
                    unset($all_group_activities[$k]);
                }
            }
        }
        if (count($all_group_activities) == 0) return true;
        //----------------------------------------------------
        //  開始檢查每一個團是否有時間相沖
        //----------------------------------------------------
        /*把trip_activity_tickets idx 轉做 把trip_activity_tickets[id];  效能上不用再query tripActivityTicket一次*/
        $trip_activity_tickets = array();
        foreach ($all_trip_activity_tickets as $v){
            $trip_activity_tickets[$v['id']] = $v;
        }
        /*檢查每團時間 TODO 考盧要否改成起始至結束時間*/
        $present_time_range = helper_generate_time_ranges($start_time, $trip_activity_ticket['qty_unit'], $trip_activity_ticket['qty_unit_type']);
        $intersection_times = array();
        foreach ($all_group_activities as $gp_activity){
            $gp_activity_ticket = $trip_activity_tickets[$gp_activity['activity_ticket_id']];
            $group_time_range = helper_generate_time_ranges($gp_activity['start_time'], $gp_activity_ticket['qty_unit'], $gp_activity_ticket['qty_unit_type']);
            $get_intersection_times_from_present_and_group = helper_get_two_time_range_intersection($present_time_range, $group_time_range);
            $intersection_times = array_merge_recursive($intersection_times, $get_intersection_times_from_present_and_group);
        }
        $count_intersection_times = array_count_values($intersection_times);
        foreach ($count_intersection_times as $k => $v){
            if($v >= $trip_activity_ticket['time_range_restrict_group_num_per_day']){
                throw new Exception('抱歉，此場次已額滿。');
            }
        }

        return $trip_activity_ticket;

    }

    function helper_create_activity_tickets_start_times_for_fix_time_ranges($trip_activity_ticket){
        $qty = $trip_activity_ticket['qty_unit'];
        $qty_unit = $trip_activity_ticket['qty_unit_type'];
        $qty_units_for_min = ['hour' => 60, 'min' => 1];
        $qty_unit_min = $qty*$qty_units_for_min[$qty_unit];
        $interval = $trip_activity_ticket['fix_time_ranges']['interval'];
        $interval_unit = $trip_activity_ticket['fix_time_ranges']['interval_unit'];
        $interval_min = $interval*$qty_units_for_min[$interval_unit];
        $trip_activity_start_time = $trip_activity_ticket['fix_time_ranges']['start_time']; //10:00
        $trip_activity_end_time = $trip_activity_ticket['fix_time_ranges']['final_time'];  //23:00
        if($trip_activity_start_time == null || $trip_activity_end_time == null){
            return false;
        }
        //-----------------------
        //  初始化
        //-----------------------
        $ref_day = '1991-01-01';
        $first_start_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$trip_activity_start_time);
        $final_start_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$trip_activity_end_time);
        if(strtotime($trip_activity_start_time) > strtotime($trip_activity_end_time) ) $final_start_dateTime->addDay(); //是否過夜
        //-----------------------
        //  建立起始時間陣列
        //-----------------------
        $time_data = array();
        while(strtotime($first_start_dateTime->toDateTimeString()) <= strtotime($final_start_dateTime->toDateTimeString())){
            array_push($time_data, $first_start_dateTime->toTimeString());
            $first_start_dateTime->addMinute($interval_min);
        }


        return $time_data;
    }
    //------------------------------------------------------------------
    //     沒有固定 Time Range function
    //
    //
    //-------------------------------------------------------------
    //  沒有固定舉辦時間的活動：
    //  params: open_time, close_time(關門時間), interval
    //  例： o: 10:00 c: 22:50 ； 開場區間： 半小時 ; 票券花時： 2小時
    //  start_times : [10:00 , 10:30 , 11:00 ..... 20:00, 20:30]
    //  因為21:00 + 2小時  = 23:00 > 22:50
    //-------------------------------------------------------------
    function helper_create_activity_tickets_start_times($open_time, $close_time, $ticket_duration, $ticket_duration_type, $interval = null, $interval_unit = null){
        if($ticket_duration == null || $ticket_duration_type == null){
            return false;
        }else{
            $ticket_duration_units = ['hour' => 60, 'min' => 1];
            $ticket_duration_min = $ticket_duration*$ticket_duration_units[$ticket_duration_type];
        }
        //場次區間init
        if($interval == null || $interval_unit == null){
            $interval_min = 30;
        }else{
            $interval_units = ['hour' => 60, 'min' => 1];
            $interval_min = $interval*$interval_units[$interval_unit];
        }
        //-----------------------
        //  初始化
        //-----------------------
        $ref_day = '1991-01-01';
        $open_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$open_time);
        $close_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$close_time);
        if($open_time > $close_time) $close_dateTime->addDay();
        //建立所有小於close time的「起始時間」
        $output = array();
        $start_DateTime = $open_dateTime; //第一場
        while(strtotime($start_DateTime->toDateTimeString()) < strtotime($close_dateTime->toDateTimeString())){
            //完結時間不能大於休息時間 starttime + finishtime < closetime
            $finish_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_DateTime->toDateTimeString())->addMinute($ticket_duration_min);
            if(strtotime($finish_time->toDateTimeString()) > strtotime($close_dateTime->toDateTimeString()) ){
                break;
            }
            array_push($output, $start_DateTime->toTimeString());
            $start_DateTime->addMinute($interval_min);
        }

        return $output;
    }
    //-------------------------------------------------------------
    //  檢查活動時間是否在營業時間內
    //  範例： 21:00開始，2小時
    //  早上10:00 - 23:00 close => 23:50
    //-------------------------------------------------------------
    function helper_time_in_fix_time_ranges($trip_activity_ticket, $start_time){
        $qty = $trip_activity_ticket['qty_unit'];
        $qty_unit = $trip_activity_ticket['qty_unit_type'];
        $start_time = date('H:i:s', strtotime($start_time));  //21:00
        $trip_activity_first_start_time = $trip_activity_ticket['fix_time_ranges']['start_time']; //10:00
        $trip_activity_final_start_time = $trip_activity_ticket['fix_time_ranges']['final_time'];  //23:00

        //-----------------------
        //  初始化
        //-----------------------
        $is_over_date = strtotime($trip_activity_first_start_time) > strtotime($trip_activity_final_start_time) ? true : false;
        $ref_day = '1991-01-01';
        $next_ref_day = '1991-01-02';
        $first_start_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$trip_activity_first_start_time);
        //final time
        $final_start_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$trip_activity_final_start_time);
        if($is_over_date) $final_start_dateTime->addDay(); //是否過夜

        //activity_start_time
        $cur_start_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ref_day.' '.$start_time);

        //判斷起始時間是否過夜 是就變隔日
        // 條件 ： 是隔夜 && 活動第一時間 >= 00:00 && 活動第一時間 < final time
        if($is_over_date && strtotime($cur_start_dateTime->toDateTimeString()) >= strtotime(date('Y-m-d H:i:s', $next_ref_day.' 00:00:00')) && strtotime($cur_start_dateTime->toDateTimeString()) <= strtotime($final_start_dateTime->toDateTimeString())){
            $cur_start_dateTime->addDay();
        }
        //activity_last_time
        //---------------------------------------------------------------------------
        $cur_activity_last_start_time = Carbon::createFromFormat('Y-m-d H:i:s', $cur_start_dateTime->toDateTimeString())->addHours($qty - 1); //21 + (2-1) = 22:00
        //-----------------------
        // 判斷時間格式
        //-----------------------
        $time_data = array();
        // 製作場次[10:00 .....  23:00] 擴充用
        $counter = 0;
        $max = 50;
        //所有場次時間
        $all_start_dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $first_start_dateTime->toDateTimeString());
        while(strtotime($all_start_dateTime->toDateTimeString()) <= strtotime($final_start_dateTime->toDateTimeString()) && ($counter <= $max)){
            if($counter == $max){
                throw new Exception();
            }
            array_push($time_data, $all_start_dateTime->toDateTimeString());
            $all_start_dateTime->addHour();
            $counter++;
        };
        //活動開始時間是否屬其中一個場次
        if(!in_array($cur_start_dateTime->toDateTimeString(), $time_data)){
            throw new Exception('沒有'.$start_time.'場次。');
        }
        if(strtotime($cur_start_dateTime->toDateTimeString()) < strtotime($first_start_dateTime->toDateTimeString()) || strtotime($cur_start_dateTime->toDateTimeString()) > strtotime($final_start_dateTime->toDateTimeString())){
            throw new Exception('沒有'.$start_time.'場次。');
        }
        return true;

    }
}