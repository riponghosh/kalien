<?php

namespace App\Services;
use App\Repositories\ErrorLogRepository;
use App\User;
use App\Services\UserService;
use App\Repositories\GuideServiceRequestRepository;

class GuideTicketService
{
	const CLASS_NAME = 'GuideTicketService';
	protected $guideServiceRequestRepository;
	protected $userService;
	protected $err_log;

	public function __construct(GuideServiceRequestRepository $guideServiceRequestRepository, UserService $userService,ErrorLogRepository $errorLogRepository)
	{
		$this->guideServiceRequestRepository = $guideServiceRequestRepository;
		$this->userService = $userService;
		$this->err_log = $errorLogRepository;
	}
//---------------------------------
//     新增預約請求 (階段一)
//---------------------------------
	//---------------------------------
	// Tourist API
	//---------------------------------
	public function get_all_guide_service_request_by_tourist($user_id, $method){
		/*$method = 'cart', 'request'*/
		if($method == 'cart'){
			$result = $this->guideServiceRequestRepository->get_all_guide_service_request($user_id, 'tourist', [1]);
		}elseif($method == 'request'){
			$result = $this->guideServiceRequestRepository->get_all_guide_service_request($user_id, 'tourist', [0,2]);
		}else{
			return ['success' => false];
		}
		if(!$result['success']) return ['success' => false];

		return ['success' => true, 'data' => $result['data']];
	}

	public function create_guide_service_request_by_tourist($order_by_id, $order_to_id, $orders){
		//-------------------------------------
		// 驗證user技能是否存在
		//-------------------------------------
		if(!$this->userService->check_user_services($order_to_id, array_pluck($orders,'service_type'), 'int')['success']){
			$this->err_log->err('service not exist',self::CLASS_NAME,__FUNCTION__);
			return ['success' => false];
		}
		//-------------------------------------
		// 把user_id加入陣列
		//-------------------------------------
		foreach ($orders as $k => $v){
			$orders[$k]['request_by_id'] = $order_by_id;
			$orders[$k]['request_to_id'] = $order_to_id;
		}
		//-------------------------------------
		// 寫入db
		//-------------------------------------
		$create_request = $this->guideServiceRequestRepository->create_guide_service_requests($orders);
		if($create_request['success'] == false) return ['success' => false];

		return ['success' => true];
	}

	public function discard_guide_service_request_by_tourist($request_id, $sent_by_id){
		$delete_query = $this->guideServiceRequestRepository->delete_guide_service_request($request_id, $sent_by_id);
		if($delete_query['success'] == false){
			$this->err_log->err('delete fail',self::CLASS_NAME,__FUNCTION__);
			return ['success' => false];
		}

		return ['success' => true];
	}
	public function edit_guide_service_request_by_tourist($request_id, $user_id, $data){
		//-------------------------------------
		// 檢查user_service
		//-------------------------------------
		if(isset($data['service_type'])){
			//取order資料中的id
			if(!$request_data = $this->guideServiceRequestRepository->get_guide_service_request_by_request_id($request_id))return ['success' => false];
			$data['service_type'] = $this->userService->get_user_service_id_by_name($data['service_type']);
			if(!$this->userService->check_user_services($request_data->request_to_id, $data['service_type'], 'int')['success']);
		}
		//-------------------------------------
		// update
		//-------------------------------------
		if(!$this->guideServiceRequestRepository->update_guide_service_request($request_id, $user_id, 'tourist', $data)['success']){
			return ['success' => false];
		};

		return ['success' => true];

	}
	//---------------------------------
	// Guide API
	//---------------------------------
	public function get_all_service_request_by_guide($user_id){
		$result = $this->guideServiceRequestRepository->get_all_guide_service_request($user_id, 'guide', [0,1]);
		if(!$result['success']) return ['success' => false];

		return ['success' => true, 'data' => $result['data']];
	}
	
	public function reject_guide_service_request_by_guide($request_id, $sent_to_id){
		$reject_request = $this->guideServiceRequestRepository->update_guide_status_at_guide_service_request($request_id, $sent_to_id, 'reject');
		if(!$reject_request['success']) return ['success' => false];

		return ['success' => true];
	}
	public function accept_guide_service_request_by_guide($request_id, $sent_to_id){
		$accept_request = $this->guideServiceRequestRepository->update_guide_status_at_guide_service_request($request_id, $sent_to_id, 'accept');
		if(!$accept_request['success']) return ['success' => false];

		return ['success' => true];
	}
	public function return_to_pending_guide_service_request_by_guide($request_id, $sent_to_id){
		$accept_request = $this->guideServiceRequestRepository->update_guide_status_at_guide_service_request($request_id, $sent_to_id, 'pending');
		if(!$accept_request['success']) return ['success' => false];

		return ['success' => true];
	}
	public function set_price_for_service_request_by_guide($request_id, $sent_to_id, $price, $price_unit){
		$update_price_query = $this->guideServiceRequestRepository->update_price_by_seller_at_guide_service_request($request_id, $sent_to_id, $price, $price_unit);
		if(!$update_price_query['success']) return ['success' => false];

		return ['success' => true];
	}
//-------------------------------------------------------------
//     請求已成票券待出售 (階段二)
//-------------------------------------------------------------
	//---------------------------------
	// 檢查票券可否發售
	// 包括：  start_date(expired), delete_at(是否已刪除), (price,unit) != null, guide_status = 1, TODO check seller avalible
	//---------------------------------
	public function check_guide_service_requests_available($requests_id, $user_id){
		$query = $this->guideServiceRequestRepository->get_available_guide_service_requests($requests_id, $user_id);
		if(count($requests_id) != count($query))
			return ['success' => false,'original_ids' => $requests_id, 'passed_id' =>  array_values(array_pluck($query, 'id'))];

		return ['success' => true];

	}
}
?>