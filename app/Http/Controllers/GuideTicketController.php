<?php
namespace App\Http\Controllers;

use App\Services\GuideTicketService;
use App\Repositories\ErrorLogRepository;
use App\Services\ScheduleService;
use App\Events\PushUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Repositories\UserProfileRepository;

class GuideTicketController extends Controller
{
	protected $guideTicketService;
	protected $scheduleService;
	protected $userService;
	protected $err_log;
	const CLASS_NAME = 'GuideTicketController';

	public function __construct(GuideTicketService $guideTicketService, ScheduleService $scheduleService, UserService $userService, ErrorLogRepository $errorLogRepository)
	{
		$this->guideTicketService = $guideTicketService;
		$this->scheduleService = $scheduleService;
		$this->userService = $userService;
		$this->err_log = $errorLogRepository;
	}
	//------------------------------
	//   tourist 在行程表上選一天中所有行程，轉成清單到前台。把清單內容變request前的準備。
	//   例：  INPUT : 時間表的其中一天，取得所有eventblock，把所有eventblock的資料制成product向guide送請求報價
	//   INPUT : date('XXXX-XX-XX'), schedule_id
	//   OUTPUT: 當天的所有行程，guide的技能。
	//------------------------------
	// API
	public function get_info_to_create_guide_ticket_order_in_a_date_by_tourist(Request $request){
		//-------------------
		//  驗證行程表中的日期
		//-------------------
		$get_schedule = $this->scheduleService->get_schedule($request->schedule_id, Auth::user()->id);
		if(!$get_schedule){$this->err_log->err('schedule : '.$request->schedule_id.' is not exist',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		$schedule = (object)$get_schedule{'data'};
		//請求人必定是遊客
		if($schedule->tourist_id != Auth::user()->id) {$this->err_log->err('user is not tourist',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		//匹配guide 是否在其中之一  TODO 之後guide會變成陣列,且Guide的邏輯會判別成是否有預約之人
		//if($schedule->guide_id != $request->guide_id) {$this->err_log->err('guide id not exist',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		//找出當天
		$date_exist = false;
		foreach($schedule->dates as $date){
			if($date{'date'} == $request->date) $date_exist = true;
		}
		if($date_exist == false) {$this->err_log->err('no event block exist',self::CLASS_NAME,__FUNCTION__); return ['success' => false,'ref_code' => '001'];};
		//------------------------------------
		//找出當天所有eventblock TODO 已購買問題
		//------------------------------------
		$order_event_blocks = array_group_by($schedule->event_blocks, 'date_start');
		$order_event_blocks = isset($order_event_blocks{$request->date}) ? $order_event_blocks{$request->date} : false;
		if(!$order_event_blocks) {$this->err_log->err('no event block',self::CLASS_NAME,__FUNCTION__); return ['success' => false,'ref_code' => '001'];};
		//------------------------------------
		// 取得guide 的 服務
		//------------------------------------
		$guide = $this->userService->get_user($schedule->guide_id);
		$guide_services = $this->userService->get_user_services($schedule->guide_id);
		if(!$guide_services['success']) {$this->err_log->err('fail to get service',self::CLASS_NAME,__FUNCTION__);return ['success' => false,'ref_code' => '002'];};
		if($guide_services['data'] == null || count($guide_services['data']) == 0) {$this->err_log->err('fail to get service',self::CLASS_NAME,__FUNCTION__);return ['success' => false,'ref_code' => '002'];};
		//------------------------------------
		// 整理
		//------------------------------------
		return [
			'success' => true ,
			'schedule_id' => $request->schedule_id,
			'guide' => ['id' => $schedule->guide_id, 'info' => $guide],
			'guide_service' => $guide_services['data'],
			'orders' =>  $order_event_blocks
		];
	}
	//------------------------------
	//   把所有行程服務轉成order request給guide
	//   例：  INPUT : 時間表的其中一天，取得所有eventblock，把所有eventblock的資料制成product向guide送請求報價
	//   INPUT : date('XXXX-XX-XX'), schedule_id, guide_id, orders(arr)[eventblockid, service]
	//------------------------------
	public function create_guide_ticket_order_by_tourist(Request $request){
		if(count($request->orders) == 0) return ['success' => false];
		//-------------------
		//  驗證行程表中的日期
		//-------------------
		$get_schedule = $this->scheduleService->get_schedule($request->schedule_id, Auth::user()->id);
		if(!$get_schedule){$this->err_log->err('schedule is not exist',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		$schedule = (object)$get_schedule{'data'};
		//請求人必定是遊客
		if($schedule->tourist_id != Auth::user()->id) {$this->err_log->err('user is not tourist',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		//匹配guide 是否在其中之一  TODO 之後guide會變成陣列
		if($schedule->guide_id != $request->servicer_id) {$this->err_log->err('guide id not exist',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		//------------------------------------
		//找出對應eventblock檢查是否存在;把eventblock資料加入訂單 TODO 已購買問題
		//------------------------------------
		$orders = $request{'orders'};
		foreach ($orders as $order => $value){
			$id = $value{'event_block_id'};
			$event_block = array_first($schedule->event_blocks,function($k, $v) use ($id){
				return $k{'id'} == $id;
			});
			if($event_block == null) {$this->err_log->err('no match evid:'.$id.'in schedule',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
			//把eventblock 資料記入order
			$orders{$order}['product_info'] = $event_block;

		}
		//-------------------------------------
		// 技能字串轉數字
		//-------------------------------------

		//-------------------------------------
		// 把eventlblock 轉做產品request
		//-------------------------------------
		$request_orders = [];
		foreach ($orders as $request_product) {
			$array = [
				'product_id' => $request_product{'product_info'}{'id'},
				'product_name' => $request_product{'product_info'}{'topic'},   //eventblock topic
				'start_date' => $request_product{'product_info'}{'date_start'},
				'start_time' => $request_product{'product_info'}{'time_start'},
				'end_date' => $request_product{'product_info'}{'date_end'},
				'end_time' => $request_product{'product_info'}{'time_end'},
				'relate_schedule_id' => $request->schedule_id,
				'service_type' => $this->userService->get_user_service_id_by_name($request_product{'user_service'})
			];
			array_push($request_orders, $array);
		}
		DB::beginTransaction();
		$create_request = $this->guideTicketService->create_guide_service_request_by_tourist(Auth::user()->id, $request->servicer_id, $request_orders);
		if(!$create_request['success']){$this->err_log->err('create order fail',self::CLASS_NAME,__FUNCTION__); return ['success' => false];};
		DB::commit();
		//------------------------------------
		// TODO lock 已訂的eventblock
		//------------------------------------

		//------------------------------------
		// TODO 推播 給guide
		//------------------------------------
		event(PushUserNotification::create()
			->user_id($request->servicer_id)->title('服務報價請求')->body('')->save()->send()
		);
		//------------------------------------
		// 成功處理
		//------------------------------------
		return ['success' => true];
	}
	public function update_guide_ticket_order_by_tourist(Request $request){

		$input = $request->only(['user_service']);
		$validator = Validator::make($input,[
			'user_service' => 'user_services'
		]);
		if($validator->fails()) {$this->err_log->err('validator:'.$input['user_service'],self::CLASS_NAME,__FUNCTION__); return ['success' => false];};

		if(!$this->guideTicketService->edit_guide_service_request_by_tourist($request->request_id, Auth::user()->id, ['service_type' => $input['user_service']])['success']){
			return ['success' => false];
		};
		return ['success' => true];

	}

	public function delete_guide_ticket_order_by_tourist(Request $request){
		if(!$this->guideTicketService->discard_guide_service_request_by_tourist($request->request_id, Auth::user()->id)['success']) return ['success' => false];
		return ['success' => true];
	}
	//-----------------------------------------------------
	//  Guide 回覆會否接受此行程
	//  INPUT: req_response(accept,reject), request_id(預訂編號)
	//-----------------------------------------------------
	public function response_guide_service_request_by_guide(Request $request){
		if($request->req_response == 'accept'){
			$result = $this->guideTicketService->accept_guide_service_request_by_guide($request->request_id, Auth::user()->id);
		}elseif($request->req_response == 'reject'){
			$result = $this->guideTicketService->reject_guide_service_request_by_guide($request->request_id, Auth::user()->id);
		}elseif($request->req_response == 'pending'){
			$result = $this->guideTicketService->return_to_pending_guide_service_request_by_guide($request->request_id, Auth::user()->id);
		} else{
			return ['success' => false];
		}

		if(!$result['success']) return ['success' => false];

		return ['success' => true];
	}
	//-----------------------------------------------------
	//  訂價： Guide 對每個行程批價
	//  INPUT: req_response(accept,reject), request_id(預訂編號)
	//-----------------------------------------------------
	public function set_price_of_guide_service_request_by_guide(Request $request){
		$validator = Validator::make($request->all(),[
			'price_unit'		=> 'currency_unit',
		]);
		if($validator->fails()) return ['success' => false];
		$result = $this->guideTicketService->set_price_for_service_request_by_guide($request->request_id, Auth::user()->id, $request->price, $request->price_unit);
		if(!$result['success']) return ['success' => false];

		return ['success' => true];
	}
	//-----------------------------------------------------
	//   購買購物車內容
	//	 1.要求第三方支付
	//   2.把貨品從（請求資料庫）轉至（票券：已購買）
	//-----------------------------------------------------
	public function purchase_guide_service_tickets(){

	}
	//-----------------------------------------------------
	//
	//              Blade
	//
	//-----------------------------------------------------
	public function show_tourist_before_order_request_form_modal(Request $request){
		$data = $this->get_info_to_create_guide_ticket_order_in_a_date_by_tourist($request);
		if(!$data['success']){
			if(isset($data['ref_code']))return ['success' => false,'ref_code' => $data['ref_code']];
			return ['success' => false];
		};
		return view('touristDashboard.touristBeforeOrderRequestFormModal', compact('data'));
	}



}
?>

