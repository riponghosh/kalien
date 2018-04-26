<?php

namespace App\Repositories;

use App\GuideServiceRequest;
use App\Repositories\ErrorLogRepository;

class GuideServiceRequestRepository
{
    protected $guideServiceRequest;
    protected $err_log;
	const CLASS_NAME = 'GuideServiceRequestRepository';

    public function __construct(GuideServiceRequest $guideServiceRequest, ErrorLogRepository $errorLogRepository)
	{
		$this->guideServiceRequest = $guideServiceRequest;
		$this->err_log = $errorLogRepository;
	}

	public function get_guide_service_request_by_request_id($request_id){
    	return $this->guideServiceRequest->where('id', $request_id)->first();
	}
	public function get_all_guide_service_request($user_id, $role, $guide_status){
    	if($role == 'tourist'){
    		$role = 'request_by_id';
		}elseif($role == 'guide'){
    		$role = 'request_to_id';
		}else{
			return ['success' => false];
		}
		return ['success' => true, 'data' => $this->guideServiceRequest->whereIn('guide_status',$guide_status)->where($role, $user_id)->get()];
	}

	public function create_guide_service_request($sent_by_id, $sent_to_id, $product_id, $product_name, $start_date, $start_time, $end_date, $end_time, $skill_type){
		/*TODO 增加判斷skill type 邏輯*/
		$query = new GuideServiceRequest();
		$query->request_by_id = $sent_by_id;
		$query->request_to_id = $sent_to_id;
		$query->product_id = $product_id;
		$query->product_name = $product_name;
		$query->start_date = $start_date;
		$query->start_time = $start_time;
		$query->end_date = $end_date;
		$query->end_time = $end_time;
		$query->service_type = $skill_type;
		$query->save();
		if($query) return ['success' => true , 'request_id' => $query->id];
		return ['success' => false];
	}
	public function create_guide_service_requests($orders){
		foreach ($orders as $k => $v){
			$orders[$k]['created_at'] = date('Y-m-d H:i:s');
			$orders[$k]['updated_at'] = date('Y-m-d H:i:s');
		}
		/*TODO 增加判斷skill type 邏輯*/
		$query = $this->guideServiceRequest->insert($orders);
		if($query) return ['success' => true];
		return ['success' => false];
	}

	public function update_guide_service_request($request_id, $user_id, $role, $data){
		if($role == 'tourist'){
			$role = 'request_by_id';
		}elseif($role == 'guide'){
			$role = 'request_to_id';
		}else{
			return ['success' => false];
		}
		$result = $this->guideServiceRequest->where('id', $request_id)->where($role, $user_id)->update($data);
		if(!$result){
			$this->err_log->err('data: '.json_encode($data),self::CLASS_NAME,__FUNCTION__);
			return ['success' => false];
		}
		return ['success' => true];
	}
	public function delete_guide_service_request($request_id, $sent_by_id)
	{
		$query = $this->guideServiceRequest->where('id', $request_id)->where('request_by_id', $sent_by_id)->delete();
		if(!$query) return ['success' => false];

		return ['success' => true];
	}

	public function update_guide_status_at_guide_service_request($request_id, $sent_to_id, $type){
		if($type == 'accept'){
			$query = $this->guideServiceRequest->where('guide_status',0)->where('id', $request_id)->whereNotNull('price_by_seller')->where('request_to_id', $sent_to_id)->update(['guide_status' => 1]);
		}elseif($type == 'reject'){
			$query = $this->guideServiceRequest->whereIn('guide_status',[0,1])->where('id', $request_id)->where('request_to_id', $sent_to_id)->update(['guide_status' => 2]);
		}elseif($type == 'pending'){
			$query = $this->guideServiceRequest->where('guide_status',1)->where('id', $request_id)->where('request_to_id', $sent_to_id)->update(['guide_status' => 0]);

		}else{
			$this->err_log->err('wrong operate type: '.$type, self::CLASS_NAME, __FUNCTION__);
			return ['success' => false];
		}

		if(!$query){ $this->err_log->err('operation: '.$type.' fail; who: '.$sent_to_id, self::CLASS_NAME, __FUNCTION__); return ['success' => false];};

		return ['success' => true];
	}

	public function update_price_by_seller_at_guide_service_request($request_id, $sent_to_id, $price, $price_unit){
		if($price_unit == null) return ['success' => false]; //TODO 設white list
		$query = $this->guideServiceRequest->whereIn('guide_status',[0,1])->where('id', $request_id)->where('request_to_id', $sent_to_id)
			     ->update(['price_by_seller' => $price, 'price_unit_by_seller' => $price_unit]);
		if(!$query) return ['success' => false];
		return ['success' => true];
	}
	//---------------------------------
	// 檢查票券可否發售
	// 包括：  start_date(expired), delete_at(是否已刪除), (price,unit) != null, guide_status = 1, TODO check seller avalible
	//---------------------------------
	public function get_available_guide_service_requests($requests_id, $user_id){
		$query = $this->guideServiceRequest->whereIn('id', $requests_id)->where('request_by_id', $user_id)
			->whereDate('start_date','>=' ,date("Y-m-d"))->whereNull('deleted_at')->where('guide_status',1)->get();
		if(!$query){
			$this->err_log->err('query fail',self::CLASS_NAME,__FUNCTION__);
		}
		return $query;
	}
}
?>

