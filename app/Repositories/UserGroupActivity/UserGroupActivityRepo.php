<?php

namespace App\Repositories\UserGroupActivity;

use App\Repositories\BaseRepository;
use Carbon\Carbon;
use App\UserActivityTicketUserGpActivity;
use App\UserGroupActivity\UserGpActivityApplicants;
use App\UserGroupActivity\UserGroupActivity;
use League\Flysystem\Exception;

class UserGroupActivityRepo extends BaseRepository
{
    protected $userActivityTicketUserGpActivity;
    protected $participant;

    function __construct(UserActivityTicketUserGpActivity $userActivityTicketUserGpActivity, UserGpActivityApplicants $userGpActivityApplicants)
    {
        $this->userActivityTicketUserGpActivity = $userActivityTicketUserGpActivity;
        $this->participant =  $userGpActivityApplicants;
        parent::__construct();

    }

    function model()
    {
        return new UserGroupActivity();
    }

    function eager_load($model){
        return $model->with('applicants','trip_activity_ticket','trip_activity_ticket.Trip_activity');
    }

    public function create($host_id, $activity_ticket_id, $activity_title, $start_date, $start_time, $start_at, $duration, $duration_unit, $need_min_joiner_for_avl_gp, $limit_joiner = null, $has_pdt_stock){
        $data = array();
        $data['host_id'] = $host_id;
        $data['activity_ticket_id'] = $activity_ticket_id;
        $data['activity_title'] = $activity_title;
        $data['start_date'] = $start_date;
        $data['start_time'] = $start_time;
        $data['start_at'] = $start_at;
        $data['timezone'] = 'Asia/Taipei'; //TODO 增加時區
        $data['duration'] = $duration;
        $data['duration_unit'] = $duration_unit;
        $data['need_min_joiner_for_avl_gp'] = $need_min_joiner_for_avl_gp;
        $data['has_pdt_stock'] = $has_pdt_stock;
        if($limit_joiner != null){
            $data['limit_joiner'] = $limit_joiner;
        }
        $data['created_at'] = date('Y-m-d H:i:s', time() );
        $data['updated_at'] = date('Y-m-d H:i:s', time() );

        $query = $this->model->insertGetId($data);
        if(!$query) return false;
        $gp_activity_id = '10001'.date('ymd').$query;
        $query = $this->model->where('id', $query)->update([
            'gp_activity_id' => $gp_activity_id
        ]);


        return $gp_activity_id;
    }
    //------------------------------------------------------
    //
    //
    //------------------------------------------------------
    public function first($id, $not_full = false, $not_expired = false){
        $query = $this->model;
        $query = $this->eager_load($query);
        if($not_expired){
            $query = $this->query_cond_gp_activities_not_expired($query);
        }
        $query = $query->find($id);
        if($not_full == true){
            $query = $this->helper_gp_activity_not_full_joiner($query);
        }

        return $query;
    }
    public function first_by_gp_activity_id($activity_id, $not_full = false, $not_expired = false){
        $query = $this->model->with('applicants')->with('trip_activity_ticket')->where('gp_activity_id', $activity_id);
        if($not_expired){
            $query = $this->query_cond_gp_activities_not_expired($query);
        }
        $query = $query->first();
        if($not_full == true){
            $query = $this->helper_gp_activity_not_full_joiner($query);
        }

        return $query;

    }
    public function get($attr = array()){
        $query = $this->model;
        $query = $this->eager_load($query);
        //----------------
        //  多少個活動
        //----------------
        if(isset($attr['limit_activities'])){
            $query = $query->limit($attr['limit_activities']);
        }else{
            $query = $query->limit(15);
        }
        $query->latest();
        //--------------------
        //  需要過開始時間
        //--------------------
        if(isset($attr['over_start_at'])){
            $query->whereDate('start_at', '<' , Carbon::now()->toDateTimeString());
        }
        //--------------------
        //  是否要未到期 預設不要
        //--------------------
        if(isset($attr['is_not_expired'])){
            $query = $this->query_cond_gp_activities_not_expired($query);
        }
        //--------------------
        //  活動期間
        //--------------------
        if(isset($attr['query_start_date'])){
            $query = $query->whereDate('start_date','>=', $attr['query_start_date'].' '.'00:00:00');
        }
        if(isset($attr['query_end_date'])){
            $query = $query->whereDate('start_date','<=', $attr['query_end_date'].' '.'23:59:00');
        }
        //--------------------
        //  成團有最少參加者限制的gp
        //--------------------
        if(isset($attr['need_min_joiner_for_avl_gp'])){
            $query->where('need_min_joiner_for_avl_gp', $attr['need_min_joiner_for_avl_gp']);
        }
        //--------------------
        //  限制只要已成團
        //--------------------
        if(isset($attr['is_achieved']) || isset($attr['is_available_group_for_limit_gp_ticket'])){
            $query->whereNotNull('achieved_at');
        }
        //--------------------
        // 指定票券 or 多張票券
        //--------------------
        if(isset($attr['activity_ticket_id'])){
            $query = $query->where('activity_ticket_id', $attr['activity_ticket_id']);
        }elseif(isset($attr['activity_ticket_ids'])){
            $query->whereIn('activity_ticket_id', $attr['activity_ticket_ids']);
        }

        $query = $query->get();
        //------------------------
        // After query
        //------------------------
        if(isset($attr['not_full'])){
            $query = $this->helper_gp_activities_not_full_joiner($query);
        }

        return $query;
    }

    public function get_by_host_id($host_id){
        $query = $this->model->where('host_id', $host_id);

        $query = $this->query_cond_gp_activities_not_expired($query);

        $query = $query->get();

        return $query;
    }

    public function get_by_participant($participant_id){
        $query = $this->model->where(function ($query) use ($participant_id){
            $query->where('host_id', $participant_id)->orWhereHas('applicants', function ($q) use ($participant_id){
                $q->where('applicant_id', $participant_id);
            });
        });
        $query = $this->query_cond_gp_activities_not_expired($query);
        $query = $query->get();

        return $query;
    }
    public function update_by_id($id,$data){
        $query =  $this->model->find($id);
        $query->update($data);
        return $query;
    }
    public function delete($id){
        $query = $this->model->find($id)->delete();
        return $query;
    }
//------------------------------------------------------------------------------
//
//    Participant
//
//------------------------------------------------------------------------------
    public function find_participant($val, $type){
        return UserGpActivityApplicants::with('Group_activity')->where($type, $val)->first();
    }
    public function create_participant($activity_id, $applicant_id, $data = array()){
        //----------------------------------------------------------------------
        // 新增參加者
        //----------------------------------------------------------------------
        $data['applicant_id'] = $applicant_id;
        $data[ 'user_gp_activity_id'] = $activity_id;
        $sql1 = UserGpActivityApplicants::create($data);
        if(!$sql1) throw new Exception('失敗，會盡快處理問題。');

        return true;
    }

    public function delete_participant($get_group, $applicant_id){
        $del_participant = UserGpActivityApplicants::where('applicant_id', $applicant_id)->where('user_gp_activity_id',$get_group['id'])->first()->delete();
        if(!$del_participant) throw new Exception('失敗，會盡快處理問題。');

        return true;
    }

    public function delete_participant_by_ticket_id($ticket_id, $gp_activity_id){
        if(!$del = UserGpActivityApplicants::where('user_gp_activity_id', $gp_activity_id)->where('ticket_id', $ticket_id)->first()->delete()){
            throw new Exception('失敗，會盡快處理問題。');
        };

        return true;

    }
    public function update_participant($id, $params){
        return $this->participant->where('id', $id)->update($params);
    }

//------------------------------------------------------------------------------
//
//    user_activity_ticket_and_gp_activity_relation
//      票券與團的pivot table 管理
//      gp:tic = 1:n
//------------------------------------------------------------------------------
    public function get_user_activity_ticket_and_gp_activity_relation($activity_ticket_id){
        $query = $this->userActivityTicketUserGpActivity->where('user_activity_ticket_id', $activity_ticket_id)->first();
        if(!$query) return false;
        return $query;
    }

    public function delete_user_activity_ticket_and_gp_activity_relation($id, $id_type){
        $query = $this->userActivityTicketUserGpActivity->where($id_type, $id)->delete();
        return $query;
    }

//------------------------------------------------------------------------------
//
//    HELPERS
//
//------------------------------------------------------------------------------
    public function query_cond_gp_activities_not_expired($query){
        $date_now = date('Y-m-d');
        $time_now = date('H:i:s');
        return $query->where(function ($q) use ($date_now, $time_now){
            $q->where('start_date', '>=' ,$date_now)->orWhere(function ($q2) use ($date_now, $time_now) {
                $q2->where('start_date', $date_now)->where('start_time', '>=', $time_now);
            });
        });
    }
    public function helper_gp_activities_not_full_joiner($query){
        if(count($query) <= 0) return $query;

        foreach ($query as $k => $q){
            if($q->limit_joiner != null && $q->limit_joiner <= count($q->applicants)){
                unset($query[$k]);
            }
        }

        return $query;
    }

    public function helper_gp_activity_not_full_joiner($q){
        if($q->limit_joiner != null && $q->limit_joiner < count($q->applicants)){
            throw new Exception('人數已滿。');
        }
        return $q;

    }

    public function query_cond_not_be_blocked_for_time_conflict($query){
        return $query->doesntHave('blocked_by_for_conflict');
    }

}
?>