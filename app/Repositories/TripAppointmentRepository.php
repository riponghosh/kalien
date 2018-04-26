<?php
namespace App\Repositories;

use App\User;
use App\Guide;
use Carbon\Carbon;
use App\GuideTouristMatch;
use App\AppointmentDate;
use App\Repositories\ErrorLogRepository;
use App\GuideServicePlace;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TripAppointmentRepository
{
    const CLASS_NAME = 'TripAppointmentRepository';
    protected $err_log;
    protected $user;
    protected $guideTouristMatch;
    protected $appointmentDate;
    protected $guide;
    /*
     * Appointment Status
     * */
    const GT_MATCH_STATUS_ACCEPT = 1;
    const GT_MATCH_STATUS_PENDING = 2;
    const GT_MATCH_STATUS_REJECT = 3;
    const GT_MATCH_STATUS_DISCARD = 10;

    public function __construct(User $user, Guide $guide, GuideTouristMatch $guideTouristMatch, AppointmentDate $appointmentDate, ErrorLogRepository $errorLogRepository){
        $this->user = $user;
        $this->guide = $guide;
        $this->guideTouristMatch = $guideTouristMatch;
        $this->appointmentDate = $appointmentDate;
        $this->err_log = $errorLogRepository;

    }

    public function get_current_tourist_all_status($current_user_id, $guide_id){
        $i = 0;
        do {
            $current_user_requests = $this->guideTouristMatch->where('tourist_id', $current_user_id)
                ->where('guide_id', $guide_id)
                ->whereIn('status', [self::GT_MATCH_STATUS_ACCEPT, self::GT_MATCH_STATUS_PENDING, self::GT_MATCH_STATUS_REJECT])->get();
            if (!$current_user_requests) {
                $this->err_log('', self::CLASS_NAME, __FUNCTION__);
            }
            $check_expired = $this->change_status_if_appointment_dates_expired($current_user_requests);
            $i++;
        }while($check_expired['has_expired'] == true && $i < 2);
        return $current_user_requests;
    }
    /*
     * 含條件的：  expired
     */
    public function get_current_guide_all_enable_request($guide_id){
        $i = 0;
        do {
            $current_user_requests = $this->guideTouristMatch->where('guide_id', $guide_id)->where('is_expired', 0)
                ->whereIn('status', [self::GT_MATCH_STATUS_ACCEPT, self::GT_MATCH_STATUS_PENDING])->get();
            $check_expired = $this->change_status_if_appointment_dates_expired($current_user_requests);
            $i++;
        } while($check_expired['has_expired'] == true && $i < 2);
        if(!$current_user_requests){
            $this->err_log('fail', self::CLASS_NAME, __FUNCTION__);
            return [];
        }
        return $current_user_requests;
    }

    public function get_current_tourist_all_enable_request($tourist_id){
        $i = 0;
        do {
            $current_user_requests = $this->guideTouristMatch->where('tourist_id', $tourist_id)->where('is_expired', 0)
                ->whereIn('status', [self::GT_MATCH_STATUS_ACCEPT, self::GT_MATCH_STATUS_PENDING])->get();
            $check_expired = $this->change_status_if_appointment_dates_expired($current_user_requests);
        } while($check_expired['has_expired'] == true && $i < 2);
        if(!$current_user_requests){
            $this->err_log('fail', self::CLASS_NAME, __FUNCTION__);
            return [];
        }
        return $current_user_requests;
    }

    public function get_trip_appointment_by_id($appointment_id, $user_id, $role){
        if($role == 'guide'){
            $role_key = 'guide_id';
        }elseif($role == 'tourist'){
            $role_key = 'tourist_id';
        }else{
            return false;
        }
        $i = 0;
        do {
            $result = $this->guideTouristMatch->where('id', $appointment_id)->where($role_key, $user_id)
                ->whereIn('status', [self::GT_MATCH_STATUS_PENDING, self::GT_MATCH_STATUS_ACCEPT, self::GT_MATCH_STATUS_REJECT])->first();
            if (!$result) return false;
            $check_expired = $this->change_status_if_appointment_dates_expired([$result]);
        }while($check_expired['has_expired'] == true && $i < 2);
        return $result;
    }
    /*
     *  N->P  T
     */
    public function create_appointment_by_tourist($tourist_id, $guide_id, $dates){
        /*現在存start-end,未來存數天*/
        if(!isset($dates['date_start']) || !isset($dates['date_end'])) return false;
        /*比較差值*/
        $date_start = Carbon::parse($dates['date_start']);
        $date_end = Carbon::parse($dates['date_end']);
        $today = Carbon::today('UTC');
        if($date_start > $date_end || $date_start < $today) return false;
        /*不能大於十天*/
        if((strtotime($date_end) - strtotime($date_start)) > (86400*10)){
            $day = (strtotime($date_end) - strtotime($date_start) - (86400*10))/86400;
            $msg = 's_day:'.$date_start.' e_day:'.$date_end.' over'.$day.'day';
            $this->err_log->err($msg, self::CLASS_NAME, __FUNCTION__);
            return ['success' => false, 'msg' => 'over ten day'];
        }
        /*限制2個pending*/
        $limit_pending_query = $this->guideTouristMatch
                                    ->where('tourist_id',$tourist_id)
                                    ->where('guide_id',$guide_id)
                                    ->where('status',self::GT_MATCH_STATUS_PENDING)
                                    ->get();
        if(count($limit_pending_query) > 1){
            $this->err_log->err('over pending limit', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        };
        DB::beginTransaction();
            $create_appointment = $this->guideTouristMatch->create([
                'guide_id'  => $guide_id,
                'tourist_id' => $tourist_id,
                'status' => self::GT_MATCH_STATUS_PENDING
            ]);
            if(!$create_appointment) return false;
            /*儲存日期*/
            $current_date = $date_end;
            $dates_data = array();
            do{
                $i = true;
                if(strtotime($current_date) >= strtotime($date_start)){
                    $date = [];
                    $date['guide_tourist_matches_id'] = $create_appointment['id'];
                    $date['date'] = $current_date;
                    array_push($dates_data, $date);
                    $current_date = date('Y-m-d',strtotime($current_date . "-1 days"));
                }else{
                    $i = false;
                }
            }while($i == true);
            /*寫入日期*/
            $insert_date_query = $this->appointmentDate->insert($dates_data);
        DB::commit();

        if(!$insert_date_query){
            $this->err_log->err('cant insert date', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }

        return ['success' => true];

    }
    /*
    *  P->A / P->R  G
    */
    public function response_appointment_by_guide($current_user_id, $guide_tourist_match_id, $method){
        if($method == 'accept'){
            $response = self::GT_MATCH_STATUS_ACCEPT;
        }elseif($method == 'reject'){
            $response = self::GT_MATCH_STATUS_REJECT;
        }else{
            $this->err_log->err('no this response', self::CLASS_NAME, __FUNCTION__);
            return false;
        }

        $query = $this->guideTouristMatch->where('guide_id' ,$current_user_id)->where('id',$guide_tourist_match_id)->where('is_expired',0)
                        ->where('status',self::GT_MATCH_STATUS_PENDING)
                        ->update(['status' => $response]);

        return $query;
    }
    /*
    *  P->N T
    */
    public function cancel_appointment_by_tourist($tourist_id, $guide_tourist_match_id){
        $action_query = $this->guideTouristMatch->where('tourist_id',$tourist_id)
                        ->where('id', $guide_tourist_match_id)
                        ->where('status', self::GT_MATCH_STATUS_PENDING)
                        ->delete();
        if(!$action_query){
            $this->err_log->err('delete failed', self::CLASS_NAME, __FUNCTION__);
            return false;
        }
        return $action_query;
    }
    /*
     *  A->D Both
     */
    public function discard_appointment_by_user($user_id, $guide_tourist_match_id){
        /**
         * 後補流程檢驗
         * 不能有已付訂至已完成的行程
         */
        $action_query = $this->guideTouristMatch
                        ->where('id',$guide_tourist_match_id)
                        ->where('status',self::GT_MATCH_STATUS_ACCEPT)->where('delete_forbidden',false)->where(function($q) use($user_id){
                            $q->orWhere('tourist_id',$user_id)->orWhere('guide_id',$user_id);
                        })->update([
                            'status' => self::GT_MATCH_STATUS_DISCARD,
                            'deleted_by' => $user_id,
                        ]);
        if(!$action_query) return false;
        return $action_query;
    }

    private function change_status_if_appointment_dates_expired($appointment_query){
        $has_expired = false;
        if(count($appointment_query) > 0) {
            foreach ($appointment_query as $trip_appointment) {
                if (count($trip_appointment->appointmentDates) > 0 && $trip_appointment->is_expired == false) {
                    $tomorrow = strtotime('tomorrow');
                    foreach ($trip_appointment->appointmentDates as $date) {
                        $app_date = strtotime($date->date);
                        if ($app_date < $tomorrow) {
                            $this->guideTouristMatch->where('id',$trip_appointment->id)->update(['is_expired' => 1]);
                            $has_expired = true;
                            break 2;
                        };
                    }
                }
            }
        }
        return ['has_expired' => $has_expired];
    }

}
?>

