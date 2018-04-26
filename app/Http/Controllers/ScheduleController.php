<?php

namespace App\Http\Controllers;

use App\Repositories\UserProfileRepository;
use App\Services\ChatRoomService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ScheduleService;
use App\Events\ScheduleEvent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;

class ScheduleController extends Controller
{

	protected $scheduleService;
	protected $chatRoomService;
	protected $userRepository;

	public function __construct(ScheduleService $scheduleService,ChatRoomService $chatRoomService,UserProfileRepository $userProfileRepository){
		$this->scheduleService = $scheduleService;
		$this->chatRoomService = $chatRoomService;
		$this->userRepository = $userProfileRepository;
	}

	public function schedule_desk($schedule_id){
		$get_schedule = $this->scheduleService->get_schedule($schedule_id,Auth::user()->id);
		if(!$get_schedule) return abort(404);
		$schedule = (object)$get_schedule{'data'};
		/*判斷user身份*/
		switch (Auth::user()->id) {
			case $schedule->guide_id:
				$other_user_id = $schedule->tourist_id;
				break;
			case $schedule->tourist_id:
				$other_user_id = $schedule->guide_id;
				break;
			default:
				return abort(404);
				break;
		}
		$members_id = array(Auth::user()->id,$other_user_id);
		/*chatroom 載入*/
		$chatroom_id = $this->chatRoomService->get_chat_room_id($members_id);
		/*取得第二個user： 名稱*/
		$other_user = $this->userRepository->get_user($other_user_id);
		return view('schedule_desk/index',compact('schedule','chatroom_id','other_user'));
	}

	public function get_schedule($schedule_id){
        $get_schedule = $this->scheduleService->get_schedule($schedule_id,Auth::user()->id);
        $schedule = (object)$get_schedule{'data'};
        switch (Auth::user()->id) {
            case $schedule->guide_id:
                $role = 'guide';
                break;
            case $schedule->tourist_id:
                $role = 'tourist';
                break;
            default:
                return ['success' => false];
                break;
        }
        return ['success' => true, 'data' => $schedule,'role' => $role];

    }

    public function get_all_schedules(){
	    $get_all_schedules = $this->scheduleService->get_all_schedules(Auth::user()->id);
        return $get_all_schedules;
    }
	public function create_schedule($guide_id,$tourist_id,$dates){  //TODO 如果改回連續日，參數要改成 $date_start, $date_end
		return  $this->scheduleService->create_schedule($guide_id,$tourist_id,$dates);
		//return $this->scheduleService->create_schedule_with_continuous_day($guide_id,$tourist_id,$date_start,$date_end);
	}
	/************************************************************
	**************Event Block************************************
	************************************************************/
	public function get_event_block(Request $request){
        return $this->scheduleService->get_event_block_by_id($request->schedule_id, $request->eventblock_id);
    }
	public function event_block(Request $request){
		$schedule_id = $request->schedule_id;
		$result = [];
		$result_to_other_user = [];
		for($i = 0; $i<count($request->cmds); $i++){
			/*過濾param*/
			$input = $request->only([
			    'cmds.'.$i.'.type','cmds.'.$i.'.date','cmds.'.$i.'.from','cmds.'.$i.'.to',
                'cmds.'.$i.'.id','cmds.'.$i.'.topic','cmds.'.$i.'.sub_title', 'cmds.'.$i.'.description',
                'cmds.'.$i.'.status'
            ]);
			$input['cmds'][$i] = array_filter($input['cmds'][$i], 'strlen');
			/*
			* 資料過濾處理
			*/
			foreach($input['cmds'][$i] as $key => $val){
			    if($val == 'true')$input['cmds'][$i][$key] = true;
                if($val == 'false')$input['cmds'][$i][$key] = false;
				/*Html Parser*/
				//if($key == 'description') $input['cmds'][$i][$key]  = Purifier::clean($input['cmds'][$i][$key]);
            }

			/*時間名稱轉換*/
			if(isset($input['cmds'][$i]['from'])){
				$hr = floor($input['cmds'][$i]['from']/60);
				if($hr < 10) $hr = '0'.$hr;
				$min = $input['cmds'][$i]['from']%60;
				if($min < 10) $min = '0'.$min;
				$input['cmds'][$i]['time_start'] = $hr.':'.$min;
				$input['cmds'][$i]['from'] = (int)$input['cmds'][$i]['from'];
			}
			if(isset($input['cmds'][$i]['to'])){
				$hr = floor($input['cmds'][$i]['to']/60);
				if($hr < 10) $hr = '0'.$hr;
				$min = $input['cmds'][$i]['to']%60;
				if($min < 10) $min = '0'.$min;
				$input['cmds'][$i]['time_end'] = $hr.':'.$min;
				$input['cmds'][$i]['to'] = (int)$input['cmds'][$i]['to'];
			}
			/*驗證*/
			$validator = $this->block_validator($input['cmds'][$i]);
			/*時間格式轉換*/
			if($validator->fails()) return $validator->errors()->all();
			/*分派*/
			switch($input['cmds'][$i]['type']){
				case 'add' :
					$response = $this->create_event_block($input['cmds'][$i],$schedule_id);
					array_push($result,$response);
					if($response['status'] == 'ok'){
						unset($input['cmds'][$i]['time_start']); 
						unset($input['cmds'][$i]['time_end']);
						$input['cmds'][$i]['id'] = $response['id'];
						array_push($result_to_other_user,$input['cmds'][$i]);}
					break;
				case 'update' :
					$response = $this->update_event_block($input['cmds'][$i],$schedule_id);
					array_push($result,$response);
					if($response['status'] == 'ok'){
						unset($input['cmds'][$i]['time_start']); 
						unset($input['cmds'][$i]['time_end']);
						array_push($result_to_other_user,$input['cmds'][$i]);
					}
					break;
				case 'del' :
					$response =  $this->delete_event_block($input['cmds'][$i],$schedule_id);
					array_push($result,$response);
					if($response['status'] == 'ok') array_push($result_to_other_user,$input['cmds'][$i]);
					break;
                case 'lock_eventBlock' :
                    $response =  $this->lock_event_block($input['cmds'][$i],$schedule_id);
                    array_push($result,$response);
                    array_push($result_to_other_user,$input['cmds'][$i]);
                    break;
				default :
					return abort('404');			
			};

		};
		/*User_id For socket*/
		$members = [];
		$get_schedule = $this->scheduleService->get_schedule($schedule_id,Auth::user()->id);
		$user_ids = $get_schedule['data'];
		if(Auth::user()->id != $user_ids['guide_id']) array_push($members,$user_ids['guide_id']);
		if(Auth::user()->id != $user_ids['tourist_id']) array_push($members,$user_ids['tourist_id']);
		event( new ScheduleEvent($result_to_other_user,$members,$schedule_id, 'event_block') );

		return $result;
	}
	public function create_event_block($request,$schedule_id){
		/*資料轉換*/
		$input = array();
		foreach($request as $cmds => $value){
			$input[$cmds] = $value;
		}
		/*預設date start end 是同一天*/
		$input['date_start'] = $input['date'];
		$input['date_end'] = $input['date'];
		/*時間格式驗證*/		
		if($this->date_format_validator($input)->fails()) return abort('404');
		/*驗證分鐘是否為10的倍數*/
		if($this->time_minimum_unit($input['time_start']) == false || $this->time_minimum_unit($input['time_end']) == false){
			return abort('404');
		};
		/*開始結束日比較*/
		$compare = $this->date_compare($input['date_start'],$input['time_start'],$input['date_end'],$input['time_end']);
		if($compare == false) return abort('404');
		/*資料寫入*/
		$create_block = $this->scheduleService->create_event_block($schedule_id,Auth::user()->id,$input);
		if($create_block == false){
			return array('status' => 'error');
		}else{
			return array('id' => $create_block['id'],'status' => 'ok');
		}
	}
	/*
	**Update
	*/
	public function update_event_block($request,$schedule_id){
		/*資料轉換*/
		$input = array();
		foreach($request as $cmds => $value){
			if($cmds != '' || $cmds != ''){
			$input[$cmds] = $value;
			}
		}
		if(isset($input['date'])){
			$date['date_start'] = $input['date'];
			$date['date_end'] = $input['date'];

			$date_valid = $this->date_format_update_validator($date);
			if($date_valid->fails()){
				return $date_valid->errors()->all();
			}else{
				$input['date_start'] = $input['date'];
				$input['date_end'] = $input['date'];
			};
		}
		/*驗證分鐘是否為10的倍數*/
		if(isset($input['time_start']) && isset($input['time_end'])){
			$time['time_start'] = $input['time_start'];
			$time['time_end'] = $input['time_end'];

			$time_valid = $this->date_format_update_validator($time);
			if($date_valid->fails()){
				return $date_valid->errors()->all();
			}
			if($this->time_minimum_unit($input['time_start']) == false || $this->time_minimum_unit($input['time_end']) == false){
				return  ['status' => 'error'];
			};
		}
		if($this->scheduleService->update_event_block($schedule_id,$input['id'],Auth::user()->id,$input)){
			return ['status' => 'ok'];
		}else{
			return  ['status' => 'error'];
		};
	}
	/*
	**Delete
	*/
	public function delete_event_block($request,$schedule_id){
		$result = [];
		if($this->scheduleService->delete_event_block($schedule_id,$request['id'],Auth::user()->id) == false){
			return ['status' => 'error'];
		}else{
			return ['status' => 'ok'];	
		};
	}
	public function lock_event_block($request,$schedule_id){
	    $result = [];
        if(!$this->scheduleService->lock_or_unlock_event_block($schedule_id,$request['id'],Auth::user()->id,$request['status'])){
            return ['status' => 'error'];
        }else{
            return ['status' => 'ok'];
        };

    }
    /*****************************************************
     **date
     *****************************************************/
    /*
     * add date
     */
    public function add_date(Request $request){
        $add_date = $this->scheduleService->add_date($request->schedule_id, Auth::user()->id, $request->new_date);
        if($add_date['success'] == false) return ['status' => 'error', 'msg' => 'wrong date'];
		/*User_id For socket*/
		$members = [];
		$get_schedule = $this->scheduleService->get_schedule($request->schedule_id, Auth::user()->id);
		$user_ids = $get_schedule['data'];
		if(Auth::user()->id != $user_ids['guide_id']) array_push($members,$user_ids['guide_id']);
		if(Auth::user()->id != $user_ids['tourist_id']) array_push($members,$user_ids['tourist_id']);

		event( new ScheduleEvent(['type' => 'addDate','date' => $request->new_date], $members, $request->schedule_id, 'schedule') ); //TODO
        return ['status' => 'ok'];
    }
    /*
     * delete date
     */
    public function delete_date(Request $request){
        $schedule_id = $request->schedule_id;
        $date = $request->date;
        if($request->force == false){
            if(!$this->scheduleService->delete_date_with_all_eventblock($schedule_id, Auth::user()->id, $date)) return ['status' => 'error'] ;
        }elseif($request->force == true){
            if(!$this->scheduleService->delete_date_with_all_eventblock_force($schedule_id, Auth::user()->id, $date)) return ['status' => 'error'] ;
        }

		/*User_id For socket*/
		$members = [];
		$get_schedule = $this->scheduleService->get_schedule($request->schedule_id, Auth::user()->id);
		$user_ids = $get_schedule['data'];
		if(Auth::user()->id != $user_ids['guide_id']) array_push($members,$user_ids['guide_id']);
		if(Auth::user()->id != $user_ids['tourist_id']) array_push($members,$user_ids['tourist_id']);

		event( new ScheduleEvent(['type' => 'deleteDate','date' => $date],$members ,$request->schedule_id, 'schedule') );
        return ['status => ok'];
	}
	/*
	**日期處理
	*/
	public function block_validator($input){
		switch($input['type']){
			case 'add' :
				$validator = Validator::make($input,[
					'temp_region' => 'boolean',
					'date' 		  => 'required|date',
					'time_start' => 'required|date_format:H:i|before_equal:time_end',
					'time_end'	 => 'required|date_format:H:i',
				]);
			break;
			case 'update' :
				$validator = Validator::make($input,[
					'id'		 => 'required',
					'temp_region' => 'boolean',
					'date' 		  => 'date',
					'time_start' => 'date_format:H:i',
					'time_end'	 => 'date_format:H:i',
				]);
			break;
			case 'del' :
				$validator = Validator::make($input,[
					'id'		 => 'required',
				]);
			break;
            case 'lock_eventBlock' :
                $validator = Validator::make($input,[
                    'status'	 => 'required'
                ]);
                break;
			default :
				$validator = Validator::make($input,[
					'type' => 'required|in:add,update,del,lock_eventBlock',
				]);
		}
		return $validator;
	}

	public function add_eventBlock_description_image(Request $request){
        if(!$request->hasFile('description_image')) return ['status' => 'error'];
        $validator = Validator::make($request->all(),[
            'media' => 'mimes:jpeg,bmp,png,gif|max:20480',
        ]);
        if($validator->fails()){
            return ['status' => 'error','msg' => $validator->errors()->all() ];
        }
        $upload_img = $this->scheduleService->create_description_image($request->description_image, Auth::user()->id);

        if($upload_img['success'] == false) return ['status' => 'error'];

        return ['status' => 'ok', 'img_path' => $upload_img['img_path']];


    }

	public function date_format_validator($input){
		Validator::extend('before_equal', function($attribute, $value, $parameters, $validator) {
			return strtotime($validator->getData()[$parameters[0]]) >= strtotime($value);
		});
		$validator = Validator::make($input,[
			'date_start' => 'required|date|before_equal:date_end',
			'date_end'	 => 'required|date',
			'time_start' => 'required|date_format:H:i',
			'time_end'	 => 'required|date_format:H:i',
		]);
		return $validator;
	}
    public function date_format_update_validator($input){
		Validator::extend('before_equal', function($attribute, $value, $parameters, $validator) {
			return strtotime($validator->getData()[$parameters[0]]) >= strtotime($value);
		});
		$validator = Validator::make($input,[
			'date_start' => 'date|before_equal:date_end',
			'date_end'	 => 'date',
			'time_start' => 'date_format:H:i',
			'time_end'	 => 'date_format:H:i',
		]);
		return $validator;
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
	/*10mins*/
	public function time_minimum_unit($time){
		$min = explode(":", $time);
		if ($min[1] % 10 != 0) {
			return false;
		}else{
			return true;
		}
	}
	public function dateDiff($startTime, $endTime) {
		$start = strtotime($startTime);
		$end = strtotime($endTime);
		$timeDiff = $end - $start;
		return floor($timeDiff / (60 * 60 * 24));
	}
}
