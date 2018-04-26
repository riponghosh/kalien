<?php
namespace App\Http\Controllers;

use App\Repositories\ErrorLogRepository;
use App\Repositories\TripAppointmentRepository;
use App\Services\ChatRoomService;
use App\Services\MediaService;
use App\Services\TransactionService;
use App\Services\TripService;
use App\Services\UserActivityTicket\ActivityTicketService;
use App\Services\UserGroupActivityService;
use App\Services\UserService;
use App\Services\GuideTicketService;
use App\User;
use App\Guide;
use App\UserLanguage;
use App\GuideServicePlace;
use Carbon\Carbon;
use App\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\RelationshipService;
use App\Repositories\UserProfileRepository;
use App\Language;
use App\GuideTouristMatch;

class UserProfileController extends Controller
{
	const CLASS_NAME = 'UserProfileController';
	protected $UserProfileRepository;
	protected $err_log;
	protected $TripAppointmentRepository;
	protected $userService;
	const GT_MATCH_STATUS_ACCEPT = 1;
	const GT_MATCH_STATUS_PENDING = 2;
	protected $relationshipService;
	protected $chatRoomService;
	protected  $TripService;
	protected $mediaService;
	protected $userGroupActivityService;
	protected $guideTicketService;
	protected $transactionService;

	public function __construct(TransactionService $transactionService, UserGroupActivityService $userGroupActivityService, GuideTicketService $guideTicketService, TripAppointmentRepository $tripAppointmentRepository, UserProfileRepository $UserProfileRepository, UserService $userService,RelationshipService $RelationshipService,TripService $TripService,ChatRoomService $chatRoomService,ErrorLogRepository $errorLogRepository,MediaService $mediaService){
		$this->UserProfileRepository = $UserProfileRepository;
		$this->TripAppointmentRepository = $tripAppointmentRepository;
		$this->err_log = $errorLogRepository;
		$this->userService = $userService;
		$this->chatRoomService = $chatRoomService;
		$this->relationshipService = $RelationshipService;
		$this->TripService = $TripService;
		$this->transactionService = $transactionService;
		$this->mediaService = $mediaService;
		$this->guideTicketService = $guideTicketService;
		$this->userGroupActivityService = $userGroupActivityService;
	}

	public function index_modal(Request $request)
	{
		$user_id = $this->UserProfileRepository->get_user_id_by_uni_name($request->uni_name);
		$user = $this->UserProfileRepository->get_user($user_id);
        $user_icon = $this->userService->get_current_use_icon_by_User($user);
		/*取得評論*/
		$review = $this->UserProfileRepository->getReviewer($user_id);
		/*行程介紹欄*/
        $trip_intros = $this->TripService->get_user_all_trip_introduction($user_id, true);
		/*appointment*/
		$user_photos = $this->UserProfileRepository->get_user_albums($user_id, 5);
		if(isset(Auth::user()->id)){
            $match_status = $this->TripAppointmentRepository->get_current_tourist_all_status(Auth::user()->id, $user_id);
		}else{
            $match_status = [];
		}
		$Friend = Auth::check() ? $this->relationshipService->get_relationship_status(Auth::user()->id,$user_id) : null;
		/*Follow(被追蹤的與追蹤的關係)*/
		$is_Follow = Auth::check() ? UserFollow::where('followed_user_id',$user_id)->where('user_id',Auth::user()->id)->first() : false;
		return view('userProfile/index_modal',compact('user','user_icon','review','match_status','schedule','is_Follow','Friend','trip_intros','user_photos'));
	}
	/*
	**show user 編輯頁
	*/
    public function showProfile_edit(Request $request){
        $user = $this->UserProfileRepository->get_current_user();
        $user_icon = $this->userService->get_current_use_icon_by_User($user);
        $guide_status = $this->UserProfileRepository->get_current_guide_status();
        /*語言列表*/
        $languages_list = Language::get(['id','language_name']);
        $is_tourGuide =  UserProfileController::auth_user_is_tourGuide();
        $is_tourist =  UserProfileController::auth_user_is_tourist();
        return view('/userProfile/userInterface/about',compact('user','user_icon','languages_list','is_tourist','guide_status','is_tourGuide'));
    }

    public function showProfile_trips_introduction(Request $request){
        $user = $this->UserProfileRepository->get_current_user();
        $user_icon = $this->userService->get_current_use_icon_by_User($user);
		$trips = $this->TripService->get_current_user_all_trip_introduction(Auth::user()->id);
    	return view('/userProfile/userInterface/tripsIntroduction',compact('user','user_icon','trips'));
	}
	public function showProfile_photos_gallery(){
        $user = $this->UserProfileRepository->get_current_user();
        $user_icon = $this->userService->get_current_use_icon_by_User($user);
		$album = $this->UserProfileRepository->get_current_user_albums();

        return view('/userProfile/userInterface/photos_gallery',compact('user','user_icon','album'));

	}
	public function show_payment(){
		$receipt = $this->transactionService->get_reciept_by_user_id(Auth::user()->id);
		if(!$receipt['success']) return abort(404);
		$user_service_tickets = $receipt['user_service_tickets'];
        $trip_activity_tickets = $receipt['trip_activity_tickets'];
		return view('/payment',compact('user_service_tickets','trip_activity_tickets'));
	}
	public function show_user_booking_request(){
		$user = $this->UserProfileRepository->get_current_user();
		$user_icon = $this->userService->get_current_use_icon_by_User($user);

		$guide_service_orders = $this->guideTicketService->get_all_guide_service_request_by_tourist(Auth::user()->id,'request');

		if(!$guide_service_orders['success']){
			$guide_service_orders = [];
			$this->err_log->err('fail to get service_request',self::CLASS_NAME, __FUNCTION__);
		}
		//-----------------------------
		// 取得 guide 資料
		//-----------------------------
		$guide_service_orders = $guide_service_orders['data'];
		foreach ($guide_service_orders as $k => $order){
			$seller = $this->userService->get_user($order['request_to_id']);
			$guide_service_orders[$k]['seller'] = $seller;
			$services = [];
			foreach ($seller->user_services as $us_service){
				$id =$us_service->service_id;
				 array_push($services, $id);
			}
			$guide_service_orders[$k]['seller_services'] = $services;
		}

		$guide_service_orders = $guide_service_orders->groupBy('relate_schedule_id');

		return view('/userProfile/userInterface/your_booking_request',compact('user','user_icon','guide_service_orders'));
	}

	public function show_user_booking_invite(Request $request){
		$user = $this->UserProfileRepository->get_current_user();
		$user_icon = $this->userService->get_current_use_icon_by_User($user);

		$guide_service_orders = $this->guideTicketService->get_all_service_request_by_guide(Auth::user()->id);
		if(!$guide_service_orders['success']){
			$guide_service_orders = [];
			$this->err_log->err('fail to get service_request',self::CLASS_NAME, __FUNCTION__);

		}
		//-----------------------------
		// 取得tourist 資料
		//-----------------------------
		$guide_service_orders = $guide_service_orders['data'];
		foreach ($guide_service_orders as $k => $order){
			$guide_service_orders[$k]['tourist'] = $this->userService->get_user($order['request_by_id']);

		}
		$guide_service_orders = $guide_service_orders->groupBy('relate_schedule_id');

		$currency_unit = $request->cookie('currency_unit');
		return view('/userProfile/userInterface/your_booking_invite',compact('user','user_icon','guide_service_orders','currency_unit'));
	}

	public function show_user_cart(){
		$user = $this->UserProfileRepository->get_current_user();
		$user_icon = $this->userService->get_current_use_icon_by_User($user);

		$get_carts = $this->transactionService->get_all_cart_items(Auth::user()->id);
		$carts = $get_carts['success'] == false ? null : $get_carts['data'];
		$guide_service_orders = $this->guideTicketService->get_all_guide_service_request_by_tourist(Auth::user()->id,'cart');

		if(!$guide_service_orders['success']){
			$guide_service_orders = []; //清空再回傳到前台
			$this->err_log->err('fail to get service_request',self::CLASS_NAME, __FUNCTION__);
		}
		//-----------------------------
		// 取得seller 資料
		//-----------------------------
		$guide_service_orders = $guide_service_orders['data'];
		foreach ($guide_service_orders as $k => $order){
			$guide_service_orders[$k]['seller'] = $this->userService->get_user($order['request_to_id']);

		}

		return view('/userProfile/userInterface/cart',compact('user','user_icon','guide_service_orders','carts'));
	}
	/*********************************************************
	**編輯profile欄位方法
	*********************************************************/
	/*
	** Summary
	*/
	public function update_currency_unit(Request $request){
		$validator = Validator::make($request->all(),[
			'currency_unit' => 'currency_unit'
		]);
		if($validator->fails()){
			return ['success' => false];
		}
		$this->UserProfileRepository->update_current_user(['default_currency_unit' => $request->currency_unit]);
		return ['success' => true];
	}

	public function edit_userProfile(Request $request)
	{
		//ref_code 0: normal ; 1: 有更新但有錯誤資料
		$ref_code = 0;
		$result = ['success' => false];
		/*
		**一般資料驗證
		*/
		$isTourist = UserProfileController::auth_user_is_tourist();
		if($isTourist == false){ 
			$validator = $this->tourist_validator($isTourist,$request);
		}else if($isTourist == true){
			$validator = $this->tourist_validator($isTourist,$request);
		}
        if($validator->fails()){
            $errors = $validator->errors();
            foreach($request->all() as $key => $value) {
                if($errors->has($key)) { //checks whether that input has an error.
                    unset($request{$key});
            	}
            }
            $result['msg'] = $validator->errors()->all();
            $result['ref_code'] = 1;

		}
		$user_data = Input::only('first_name','last_name','birth_date','phone_area_code','phone_number','sex','country','intro_video');
		/*合併name*/
		if(isset($user_data['last_name']) && isset($user_data['first_name']) ){
			$user_data['name'] = $user_data['first_name'].' '.$user_data['last_name'];
			unset($user_data['last_name']);
			unset($user_data['first_name']);
		}
		/*合併phone*/
		if(!isset($user_data['phone_number'])){
			unset($user_data['phone_area_code']);
		}
		/*
		**資料寫入
		*/
		if($this->UserProfileRepository->update_current_user($user_data) == false)return $result;

		$result['success'] = true;
		return $result;
	}
	public function edit_userProfile_img(Request $request){
		$validator = Validator::make($request->all(),[
			'userIconOrgin' => 'mimes:jpeg,bmp,png,gif,jpg|max:10240',
			'userIcon'      => 'mimes:jpeg,bmp,png,gif,jpg|max:10240'
		]);
		if($validator->fails()){
			return ['success' => false, 'msg' => 'Forbidden image Format'];
		}
		if(!$request->hasFile('userIconOrgin') || !$request->hasFile('userIcon')){
			return ['success' => false, 'msg' => 'Reupload your icon please'];
		}
		if($this->userService->upload_and_set_user_icon($request->file('userIconOrgin'),$request->file('userIcon'),Auth::user()->id)){
			return ['success' => true];
		}else{
			return ['success' => false , 'msg' => 'upload fail'];
		}

	}
	public function edit_userProfile_photo(Request $request){
        if(!$request->hasFile('photo')){
            return ['success' => false, 'msg' => 'Reupload your icon please'];
        }
        $validator = Validator::make($request->all(),[
            'photo' => 'mimes:jpeg,bmp,png,gif,jpg|max:10240',
			'p_order' => 'integer|Between:1,5'
        ]);
        if($validator->fails()){
            return ['success' => false, 'msg' => $validator->errors()->all()];
        }
        if(!$this->userService->upload_user_photo($request->photo, $request->p_order, Auth::user()->id)) return ['success' => false, 'msg' => 'upload fail'];
        return ['success' => true];
	}
	public function edit_userProfile_photo_description(Request $request){
		if(!$this->userService->update_user_photo_description($request->description, $request->photo_id, Auth::user()->id)) return ['success' => false];
		return ['success' => true];
	}
	public function delete_userProfile_photo(Request $request){
		$delete_photo = $this->userService->remove_user_photo($request->photo_id, Auth::user()->id);
		if(!$delete_photo) return ['success' => false];
		return ['success' => true];
	}
	/*
	** guideProfile
	*/
	public function edit_guideProfile(Request $request)
	{
		/*Input 驗證*/
		$validator = $this->guide_validator($request);
		if($validator->fails()) return ['success' => false];
		/*處理空input;InputOnly過濾*/
		$guide_data = Input::only('charge_per_day');
		$guide_data = array_filter($guide_data,'strlen');
		/*處理語言列表*/
		$lan_datas = $this->sort_user_language();
		/*
		**資料寫入
		*/
		$this->UserProfileRepository->update_user_language($lan_datas,Auth::user()->id);
		if($request->service_country != '') {
			$udpate_service_place = $this->UserProfileRepository->update_guide_service_places($request->service_country, $request->serviceRegion);
			if($udpate_service_place['success'] == false) return ['success' => false, 'msg' => $udpate_service_place['msg']];
        }
		return ['success' => true];
	}
	public function edit_userProfile_service(Request $request){
		/*Input 驗證*/
		$validator = $this->user_service_validator($request);
		if($validator->fails()) return ['success' => false];
		if($request->charge_per_day != null || $request->charge_per_day_cur_select != null){
            if(!$this->UserProfileRepository->update_current_guide($request->charge_per_day, $request->charge_per_day_cur_select) )return ['success' => false];
		}
		if(!$this->UserProfileRepository->update_user_service($request->user_service, Auth::user()->id)['success']) return ['success' => false];
		return ['success' => true];
	}
	/*
	**遊客申請
	*/
	public function show_tourist_apply_form(){
		Guide::firstOrCreate(['user_id' => Auth::user()->id]);
		$user = User::where('id',Auth::user()->id)->first();
		$guide = Guide::where('user_id',Auth::user()->id)->first();
		$languages_list = Language::get(['id','language_name']);
		if(!UserProfileController::auth_user_is_tourist()){
			return view('userProfile/tourist_apply_form_modal',compact('user','languages_list','guide'));
		}else{
			return 'error';
		}
	}	
	public function apply_tourist(Request $request){
		$user_id = Auth::user()->id;
		$validator = $this->user_validator($request);
		if($validator->fails()){
				return ['status' => 'error', 'msg' => $validator->errors()->all()];
		}
		/*
		**資料寫入
		*/
		/*先新增導遊欄位*/
		Guide::firstOrCreate(['user_id' => $user_id]);
		$user_data = Input::only('first_name','last_name','birth_date','phone_number','sex','country');
		/*合併name*/
		if(isset($user_data['last_name']) && isset($user_data['first_name']) ){
			$user_data['name'] = $user_data['first_name'].' ' .$user_data['last_name'];
			unset($user_data['last_name']);
			unset($user_data['first_name']);
		}
        /*合併phone*/
        if(!isset($user_data['phone_number'])){
            unset($user_data['phone_area_code']);
        }
		//過濾沒有輸入的值
		$user_data = array_filter($user_data,'strlen');
		if(count($user_data)>0){
			if($this->UserProfileRepository->update_current_user($user_data) == false) return array('status' => 'error');
		};
		/*判斷基本遊客資料是否齊全*/
		if(!UserProfileController::auth_user_is_tourist()) return array('status' => 'not_complete');
		return array('status' => 'success');		
	}	
	/*
	**導遊申請
	*/
	//是否願意做當地伙伴
	public function change_guide_status(Request $request){
		return $this->UserProfileRepository->update_current_guide_status($request->status);
	}
	//由become Guide 導引，建立導由欄位且跳至editProflie
	public function apply_guide(Request $request){
		$this->UserProfileRepository->create_current_guide();
		return $this->showProfile_edit($request);
	}
	/*檢查user有否資格預約導遊，有：發requestForm(show_appointment_request_form)*/
	public function check_appointment_request(Request $request){
		if($request->guide_id == Auth::user()->id || $request->guide_id == null) return abort(404);
		$data = array('status' => 'true',
					  'url'	   =>  'GET/appointment_form/'.$request->guide_id,
		);
		return $data;
	}
	public function show_appointment_request_form(Request $request){
		if(Auth::user()->id == $request->guideId) return abort(404);
		if(!UserProfileController::auth_user_is_tourist()) return abort(404);
		$guide = User::where('id',$request->guideId)->first();
		/*傳chatroom id 給留言用*/
		$chatroom = array('id' => '');
		$members = array(Auth::user()->id,$request->guideId);
		$chatroom['id'] = $this->chatRoomService->get_id_or_create_chat_room($members);
		return view('/userProfile/appointment_form_modal',compact('guide','chatroom'));
	}
	public function send_appointment_request_to_guide(Request $request){
		$data = array();
		$data['success'] = true;
		$validator = Validator::make($request->all(),[
			'date_start' => 'required|date|after:yesterday|before_equal:date_end',
			'date_end'	 => 'required|date|after:yesterday'
		]);
		if($validator->fails()){
            $this->err_log->err('validator: '.json_encode($validator->errors()->all()),self::CLASS_NAME, __FUNCTION__);
			$data['msg'] = $validator->errors()->all();
			$data = [
				'success' => false,
				'msg' => $validator->errors()->all()
			];
			return $data;
		}
		/*filter*/
		$appointment_request = Input::only('date_start','date_end','guide_id');
		if(!$this->relationshipService->send_friend_request($appointment_request['guide_id'],Auth::user()->id)){
            $this->err_log->err('send t friend request fail',self::CLASS_NAME, __FUNCTION__);
            $data['success']  = false;
            return $data;
		}
		/*create appointment*/
		$create_appointment = $this->TripAppointmentRepository->create_appointment_by_tourist(
            Auth::user()->id,
            $appointment_request['guide_id'],
            ['date_start' => $appointment_request['date_start'], 'date_end' => $appointment_request['date_end']]
        );
		if($create_appointment['success'] == false){
            $this->err_log->err('create appointment fail',self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
		}
		if($request->content != '' && $request->content != null){
			$send_msg = $this->chatRoomService->send_msg(Auth::user()->id, $request->room_id, $request->content);
			if($send_msg['success'] == false) $this->err_log->err('send msg fail',self::CLASS_NAME, __FUNCTION__);
		};
		return $data;
	}
	/*tourist擁有權限*/
	public function cancel_appointment_for_guide($appointment_id){
		if($this->TripAppointmentRepository->cancel_appointment_by_tourist(Auth::user()->id, $appointment_id) == 1){
			$data['success'] = true;
			return $data;
		}else{
			$data['success'] = false;
			return $data;
		};
	}
	/*guide擁有權限*/
	public function reject_appointment_by_guide(Request $request){
		if($this->TripAppointmentRepository->response_appointment_by_guide(Auth::user()->id, $request->appointment_id, 'reject') == 1){
			$data['success'] = true;
			return $data;
		}else{
			$data['success'] = false;
			return $data;
		};

	}
	public function accept_appointment_by_guide(Request $request){
		$request = Input::only('appointment_id');
		/*
		 * 取得資訊 1. 判斷appointment id 是否正確，2. 取得tourist_id
		 * */
		$appointment_exist = GuideTouristMatch::where('status',self::GT_MATCH_STATUS_PENDING)
								->where('guide_id',Auth::user()->id)
								->where('id',$request['appointment_id'])
								->first();
		if(!$appointment_exist){
			$this->err_log->err('pending not exist', self::CLASS_NAME, __FUNCTION__);
			return ['success' => false];
		}
		$schedule_dates = array();
		foreach($appointment_exist->appointmentDates as $date){
			array_push($schedule_dates, $date['date']);
		}
		/*
		 * 增加為朋友
		 * */
        if(!$this->relationshipService->accept_friend_request($appointment_exist['tourist_id'],Auth::user()->id)){
            $this->err_log->err('add friend problem; tourist :'.$appointment_exist['tourist_id'], self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
		}
		/*新增行程表*/
		$ScheduleController = app('App\Http\Controllers\ScheduleController');
		/*true : _id ; false : false */
		$create_schedule = $ScheduleController->create_schedule(
            $appointment_exist['guide_id'],
            $appointment_exist['tourist_id'],
            $schedule_dates
		);
		if($create_schedule == false) return ['success' => false];
		$schedule_id = $create_schedule{'_id'};
		/*更改為接受請求*/
		DB::beginTransaction();
		if(!$this->TripAppointmentRepository->response_appointment_by_guide(Auth::user()->id, $request['appointment_id'], 'accept')){
			return ['success' => false];
		}
		$insert_id = GuideTouristMatch::where('status',self::GT_MATCH_STATUS_ACCEPT)
					 ->where('guide_id',Auth::user()->id)
					 ->where('id',$request['appointment_id'])
					 ->update(['schedule_id'=>$schedule_id ]);
		if(!$insert_id){
            $this->err_log->err('cant insert schedule_id:'.$schedule_id, self::CLASS_NAME, __FUNCTION__);
			return ['success' => false];
        }
		DB::commit();
		return ['success' => true];
	}
	public function discard_appointment_by_user_id(Request $request){
		if(!$this->TripAppointmentRepository->discard_appointment_by_user(Auth::user()->id,$request->appointment_id)) return ['success'=>false];
		return ['success' => true];
	}
	public function show_trip_appointment_request_info(Request $request){
		$trip_appointment = $this->TripAppointmentRepository->get_trip_appointment_by_id($request->appointment_id, Auth::user()->id,'guide');
		if(!$trip_appointment) return abort(404);
		if($trip_appointment->is_expired == true){
            $tourist = $this->UserProfileRepository->get_user($trip_appointment->tourist_id);
            if(!$tourist) return abort(404);
            $chat_room = $this->chatRoomService->get_chat_room_with_last_rows_mgs_by_members_id([Auth::user()->id,$trip_appointment->tourist_id], 5);
            $chat_room = $chat_room == false ? null : $chat_room;
            return view('/userProfile/trip_appointment_request_info_status_expired',compact('tourist','trip_appointment','chat_room'));
		}elseif($trip_appointment->status == 2){
            $tourist = $this->UserProfileRepository->get_user($trip_appointment->tourist_id);
            if(!$tourist) return abort(404);
            $chat_room = $this->chatRoomService->get_chat_room_with_last_rows_mgs_by_members_id([Auth::user()->id,$trip_appointment->tourist_id], 5);
            $chat_room = $chat_room == false ? null : $chat_room;
            return view('/userProfile/trip_appointment_request_info',compact('tourist','trip_appointment','chat_room'));
		}elseif($trip_appointment->status == 1){
            return view('/userProfile/trip_appointment_request_info_status_accept',compact('trip_appointment'));
		}elseif($trip_appointment->status == 3){
			return abort(404); //可換成行程已拒絕

		}
	}

	public function show_trip_appointment_response_info(Request $request){
        $trip_appointment = $this->TripAppointmentRepository->get_trip_appointment_by_id($request->appointment_id, Auth::user()->id,'tourist');
        if(!$trip_appointment) return abort(404);
        if($trip_appointment->is_expired == true){
            $guide = $this->UserProfileRepository->get_user($trip_appointment->guide_id);
            if(!$guide) return abort(404);
            $chat_room = $this->chatRoomService->get_chat_room_with_last_rows_mgs_by_members_id([Auth::user()->id,$trip_appointment->guide_id], 5);
            $chat_room = $chat_room == false ? null : $chat_room;
            return view('/userProfile/trip_appointment_response_info_status_expired',compact('guide','trip_appointment','chat_room'));
        }elseif($trip_appointment->status == 2){
            $guide = $this->UserProfileRepository->get_user($trip_appointment->guide_id);
            if(!$guide) return abort(404);
            $chat_room = $this->chatRoomService->get_chat_room_with_last_rows_mgs_by_members_id([Auth::user()->id,$trip_appointment->guide_id], 5);
            $chat_room = $chat_room == false ? null : $chat_room;
            return view('/userProfile/trip_appointment_response_info',compact('guide','trip_appointment','chat_room'));
        }elseif($trip_appointment->status == 1){
            return view('/userProfile/trip_appointment_response_info_status_accept',compact('trip_appointment'));
        }elseif($trip_appointment->status == 3){
            $guide = $this->UserProfileRepository->get_user($trip_appointment->guide_id);
            if(!$guide) return abort(500);
            $chat_room = $this->chatRoomService->get_chat_room_with_last_rows_mgs_by_members_id([Auth::user()->id,$trip_appointment->guide_id], 5);
            $chat_room = $chat_room == false ? null : $chat_room;
            return view('/userProfile/trip_appointment_response_info_status_rejected',compact('guide','trip_appointment','chat_room'));
        }else{
            return abort(404);
		}
	}
    /*****************************************************
     **tourist Function
     ******************************************************/
    public function show_plans_modal(){
    	//------------------------------------------------
		//行程
		//------------------------------------------------
        $current_user_requests = $this->TripAppointmentRepository->get_current_tourist_all_enable_request(Auth::user()->id);
        /*最後一句留言*/
        foreach($current_user_requests as $tourist){
            $members = array($tourist['guide_id'],Auth::user()->id);
            $room_id = $this->chatRoomService->get_chat_room_id($members);

			$msg = $this->chatRoomService->get_chat_room_with_last_rows_msg($room_id, Auth::user()->id, 1);
			if($msg['success'] == true && count($msg['contents']) > 0){
				$tourist['room_id'] = $room_id;
				$tourist['msg'] = $msg['contents'][0]->content;
				$tourist['msg_sentby'] =  $msg['contents'][0]->sent_by;
			}else{
				$tourist['room_id'] = $room_id;
			};

        }
        //------------------------------------------------
        // 團體活動
        //------------------------------------------------
		$get_user_gp_activities = $this->userGroupActivityService->get_by_participant(Auth::user()->id);
		if(!$get_user_gp_activities){
			$user_gp_activities = null;
		}else{
            $user_gp_activities = $get_user_gp_activities;
		}
        return view('/touristDashboard/plans_modal',compact('current_user_requests','user_gp_activities'));
    }
	/*****************************************************
	**Guide Function
	******************************************************/
	public function show_tourist_request_dashboard_modal(){
		/*取得發出請求*/
        $request_tourist = $this->TripAppointmentRepository->get_current_guide_all_enable_request(Auth::user()->id);
		/*最後一句留言*/
		foreach($request_tourist as $tourist){
            $members = array($tourist['tourist_id'],Auth::user()->id);
            $room_id = $this->chatRoomService->get_chat_room_id($members);

            $msg = $this->chatRoomService->get_chat_room_with_last_rows_msg($room_id, Auth::user()->id, 1);
            if($msg['success'] == true && count($msg['contents']) > 0){
                $tourist['room_id'] = $room_id;
                $tourist['msg'] = $msg['contents'][0]->content;
                $tourist['msg_sentby'] =  $msg['contents'][0]->sent_by;
            }else{
                $tourist['room_id'] = $room_id;
            };
		}
		return view('/guideDashboard/touristRequest_modal',compact('request_tourist'));
	}
//------------------------------------------------------------------------------------------------------
//   User Ticket
//------------------------------------------------------------------------------------------------------
	public function show_user_ticket_index(ActivityTicketService $activityTicketService){
		$user_activity_tickets = $activityTicketService->get_all([
			'owner_id' => Auth::user()->id,
			'get_available_status' => true
		]);
		$beneficiary_incidental_tickets = $this->userService->get_beneficiary_incidental_tickets(Auth::user()->id);
        $beneficiary_incidental_tickets_is_used = $this->userService->get_beneficiary_incidental_tickets_is_used(Auth::user()->id, 3);

		return view('/userTicket/main', compact('user_activity_tickets','beneficiary_incidental_tickets', 'beneficiary_incidental_tickets_is_used'));
	}
	/*****************************************************
	**validator
	*****************************************************/
	/*
	**tourist validator
	*/
	public function tourist_validator($is_tourist,$request){
		if($is_tourist == 0){
			return $validator = Validator::make($request->all(),[
				'country'		=> 'numeric|Between:001,900',
				'first_name' 	=> 'Between:1,20|required',
				'last_name' 	=> 'Between:1,20|required',
				'sex'		 	=> 'in:M,F',
				'birth_date'	=> 'date_format:Y-m-d|before:tomorrow',
				'phone_number'	=> 'AlphaDash|Between:6,12',
                'phone_area_code'		=> 'in:886,852,853,81',
				'intro_video'		=> 'youtube_url'
			]);
		}elseif($is_tourist == 1){
			return 	$validator = Validator::make($request->all(),[
				'country'		=> 'numeric|Between:001,900|required',
				'first_name' 	=> 'Between:1,20|required',
				'last_name' 	=> 'Between:1,20|required',
				'sex'		 	=> 'in:M,F|required',
                'phone_area_code'		=> 'in:886,852,853,81|required',
				'birth_date'	=> 'date_format:Y-m-d|before:tomorrow|required',
				'phone_number'	=> 'AlphaDash|Between:6,12|required',
                'intro_video'		=> 'youtube_url',
			]);
		}
	}
	public function user_validator($request){
			return $validator = Validator::make($request->all(),[
				'country'				=> 'numeric|Between:001,900',
				'first_name' 			=> 'Between:1,20',
				'last_name' 			=> 'Between:1,20',
				'sex'		 			=> 'in:M,F',
				'birth_date'		 	=> 'date_format:Y-m-d|before:tomorrow',
				'phone_area_code'		=> 'in:886,852,853,81',
				'phone_number'			=> 'AlphaDash|Between:6,12',
				'languages_fluent'  	=> 'array',
				'languages_fluent.*'	=> 'Integer|Between:01,10',
				'languages_familiar'  	=> 'array',
				'languages_familiar.*'	=> 'Integer|Between:01,10'
			]);		
	}
	/*
	**guide validator
	*/
	public function guide_validator($request){
		return 	$validator = Validator::make($request->all(),[
			'service_country'		=> 'in:jp,tw,hk,mo,kp',
			'languages_fluent'  	=> 'array',
			'languages_fluent.*'	=> 'Integer|Between:01,10',
			'languages_familiar'  	=> 'array',
			'languages_familiar.*'	=> 'Integer|Between:01,10'
		]);
	}
	/*
	** userService validator
	*/
	public function user_service_validator($request){
		return $validator = Validator::make($request->all(),[
            'currency_unit'         => 'in:TWD,JPY,HKD',
            'charge_per_day'        => 'Between:0,1000000|integer',
			'user_service.*' => 'in:us_assistant,us_photographer,us_translator'
		]);
	}
	/*把fluent跟familiar 分類*/
	public function sort_user_language(){
		/*
		**語言(fluent跟familiar相同，取fluent)
		*/
		$language_datas = array();
		if(isset($_POST['languages_fluent'])){			
			foreach($_POST['languages_fluent'] as $val){
				$language['language_id'] = $val;
				$language['level'] = 3;
				array_push($language_datas,$language);
			};
			unset($val);
		}
		if(isset($_POST['languages_familiar'])){
			if(isset($_POST['languages_fluent'])){
				$languages_familiar = array_diff($_POST['languages_familiar'],$_POST['languages_fluent']);
			}else{
				$languages_familiar = $_POST['languages_familiar'];
			}			
			foreach($languages_familiar as $val){
					$language['language_id'] = $val;
					$language['level'] = 2;
					array_push($language_datas,$language);
			};
			unset($val);
		}
		return $language_datas;
	}
	static function auth_user_is_tourist(){
		if(!isset(Auth::user()->id))return false; 
		if(Auth::user()->name == null || Auth::user()->name == '')return false;
		if(Auth::user()->sex == null || Auth::user()->sex == '')return false;
		if(Auth::user()->country == null || Auth::user()->country == '')return false;
		if(Auth::user()->phone_number == null || Auth::user()->phone_number == '')return false;
		if(Auth::user()->birth_date == null || Auth::user()->birth_date == '')return false;
		return true;
	}
	static function auth_user_is_tourGuide(){
		if(!UserProfileController::auth_user_is_tourist()) return false;
		if(!isset(Auth::user()->id)){
			return false;
		}else{
			$user_id = Auth::user()->id;
		}; 
		$query = Guide::where('user_id',$user_id)->whereNotNull('service_country')->whereNotIn('service_country',[''])->first();
		$lanQuery = UserLanguage::where('user_id',$user_id)->first();
		if(!$query || !$lanQuery){
			return false;
		}else{
			return true;
		}
	}

}
?>

