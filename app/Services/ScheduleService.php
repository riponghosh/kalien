<?php
namespace App\Services;

use App\Repositories\ScheduleRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\MediaRepository;
use Carbon\Carbon;


class ScheduleService{

	protected $scheduleRepository;
	protected $err_log;
	protected $mediaRepository;

	const CLASS_NAME = 'ScheduleService';

	public function __construct(ScheduleRepository $ScheduleRepository, ErrorLogRepository $errorLogRepository, MediaRepository $mediaRepository)
    {
        $this->scheduleRepository = $ScheduleRepository;
        $this->err_log = $errorLogRepository;
        $this->mediaRepository = $mediaRepository;
    }
	/*
	|Create Schedule
	|   連續日期dates(array);
	|   不連續date_start,date_end
	*/
	public function create_schedule($guide_id,$tourist_id,$dates){
		$create_schedule = $this->scheduleRepository->create_schedule($guide_id,$tourist_id,$dates);
		if($create_schedule['success'] == false){
			$err_msg = isset($create_schedule['message']) ?  $create_schedule['message'] : null;
            $this->err_log->err('api failed reason : '.$create_schedule['message'], self::CLASS_NAME, __FUNCTION__);
            return false;
        }
        $new_schedule_id = $create_schedule{'_id'};
        $record_schedule = $this->scheduleRepository->user_add_schedule($new_schedule_id,$tourist_id);
        if($record_schedule['success'] == false){
            $this->err_log->err('fail to add userSchedule', self::CLASS_NAME, __FUNCTION__);
            return false;
		}
        return $create_schedule;
	}
	public function create_schedule_with_continuous_day($guide_id,$tourist_id,$date_start,$date_end){
		$dates = array();
		$date_s = Carbon::createFromFormat('Y-m-d',$date_start);
		$dateDiff = $this->dateDiff($date_start,$date_end);
		for($i=0;$i<=$dateDiff;$i++){
			$date = $date_s->toDateString();
			array_push($dates,$date);
			$date = $date_s->addDay();
		};
		return $this->scheduleRepository->create_schedule($guide_id,$tourist_id,$dates);
	}
	public function get_schedule($schedule_id,$user_id){
		$get_schedule = $this->scheduleRepository->get_schedule($schedule_id,$user_id);
        if($get_schedule{'success'} == false) return false;
        if($get_schedule{'data'} == null) return false;
        return $get_schedule;
	}

	public function get_all_schedules($user_id){
		$result = $this->scheduleRepository->get_all_schedule_by_user_id($user_id);
		if($result['success'] == false) return ['success' => false];

		$schedules = [];
		if(count($result['data']) > 0) {
            foreach ($result['data'] as $k => $v) {
                $schedule_id = $v->schedule_id;
                $get_schedule = $this->scheduleRepository->get_schedule($schedule_id,$user_id);
                if($get_schedule['success'] == true){
                	if($get_schedule{'data'} != null){
                        array_push($schedules, $get_schedule{'data'});
					}
				}
            }
        }
		return ['success' => true, 'data' => ['schedules' =>$schedules]];
	}
	public function create_date($created_by,$date){
		$created_by_user_id = $created_by;

	}
	/************************************************
	**Event Block Operate
	***********************************************/
	public function create_event_block($schedule_id,$user_id,$request){
        $input_description = isset($request['description']) ? $request['description'] : null;
        /*尚欠topic sub_title等彈性資料*/
		$result = $this->scheduleRepository->add_eventblock(
			(string)$schedule_id,
			(int)$user_id,
			$request['date_start'],
			$request['date_end'],
			$request['time_start'],
			$request['time_end'],
			$request['topic'],
			$request['sub_title'],
			false,
			false,
			NULL,
            $input_description
		);
		return $result['success'] == 'true' ? $result : false;
	}

	public function get_event_block_by_id($schedule_id, $eventblock_id){
		return $result = Schedule::where('_id',(int)$schedule_id)->get(['event_blocks']);
	}

	public function update_event_block($schedule_id,$block_id,$user_id,$data){
		$date_start = isset($data['date_start']) ? $data['date_start'] : null;
		$date_end = isset($data['date_end']) ? $data['date_end'] : null;
		$time_start = isset($data['time_start']) ? $data['time_start'] : null;
		$time_end = isset($data['time_end']) ? $data['time_end'] : null;
		$topic = isset($data['topic']) ? $data['topic'] : null;
		$sub_title = isset($data['sub_title']) ? $data['sub_title'] : null;
        $description = isset($data['description']) ? $data['description'] : null;
		$temp_region = isset($data['temp_region']) ? $data['temp_region'] : null;

		$result =  $this->scheduleRepository->update_eventblock(
			$schedule_id,
			$block_id,
			$user_id,
			$date_start,
			$date_end,
			$time_start,
			$time_end,
			$topic,
			$sub_title,
			$temp_region,
            $description
		);
		return $result['success'] == 'true' ? $result : false;
	}
	/*
	**DELETE
	*/
	public function delete_event_block($schedule_id,$block_id,$user_id){
		$result =  $this->scheduleRepository->del_eventblock($schedule_id,$block_id,$user_id);
		return $result['success'] == 'true' ? $result : false;
	}
	public function date_compare($date_start,$time_start,$date_end,$time_end){
		$start = Carbon::createFromFormat('Y-m-d H:i', $date_start.' '.$time_start);
		$end = Carbon::createFromFormat('Y-m-d H:i', $date_end.' '.$time_end);
		if( strtotime($start) > strtotime($end) ){
			return false;
		}elseif( strtotime($start) == strtotime($end) ){
			return true;
		}elseif(strtotime($start) < strtotime($end) ){
			return true;
		}else{
			return false;
		}		
	}
	/*
	 * Lock eventBlock
	 */
	public function lock_or_unlock_event_block($schedule_id,$block_id,$user_id,$lock){
	    if($lock == true) {
            $result = $this->scheduleRepository->lock_eventblock($schedule_id, $block_id, $user_id);
            return $result['success'] == true ? $result : false;
        }elseif($lock == false){
            $result = $this->scheduleRepository->unlock_eventblock($schedule_id, $block_id, $user_id);
            return $result['success'] == true ? $result : false;
        };
    }
    /*
     * * Date
     */
    public function add_date($schedule_id, $user_id, $date){
		$add_date = $this->scheduleRepository->add_date($schedule_id, $user_id, $date);
		if($add_date['success'] == false) return ['success' => false];
		return ['success' => true];
	}
    public function delete_date_with_all_eventblock($schedule_id, $user_id, $date){
        $result = $this->scheduleRepository->delete_date_with_all_eventblocks($schedule_id, $user_id, $date);
        return $result['success'] == true ? $result : false;
    }
    //強制移除(包含locked)
    public function delete_date_with_all_eventblock_force($schedule_id, $user_id, $date){
        $unlock_all_event_block = $this->scheduleRepository->unlock_all_eventblocks_in_date($schedule_id, $user_id, $date);
        if($unlock_all_event_block['success'] == false) return false;
        $result = $this->scheduleRepository->delete_date_with_all_eventblocks($schedule_id, $user_id, $date);
        return $result['success'] == true ? $result : false;

    }
    
    public function create_description_image($image, $user_id){
        $path = 'eventblock/description';
        $name = sha1('evBlock_description'.time().$user_id);
        $insert_image = $this->mediaRepository->upload_media($image, $path, $name, $user_id);
        if($insert_image['success'] == false) return ['success' => false];
        return ['success' => true, 'img_path' => $insert_image['standard_path']];
    }
   
	/*
	 * other
	 */
	public function dateDiff($startTime, $endTime) {
		$start = strtotime($startTime);
		$end = strtotime($endTime);
		$timeDiff = $end - $start;
		return floor($timeDiff / (60 * 60 * 24));
	}
}
?>

