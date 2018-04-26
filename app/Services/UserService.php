<?php
namespace App\Services;

use App\Exceptions\CurrentUser\CurUserUpdateInfoFail;
use App\Repositories\UserProfileRepository;
use App\Repositories\MediaRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\UserTicketRepository;
use Illuminate\Support\Facades\DB;

class UserService
{
	const CLASS_NAME = 'UserService';
    const User_icon_orginal_path = 'userIconOrginal';
    const User_icon_path = 'userIcon';

    protected $userProfileRepository;
    protected $userTicketRepository;
    protected $mediaRepository;
    protected $err_log;

    public function __construct(UserTicketRepository $userTicketRepository, UserProfileRepository $userProfileRepository, MediaRepository $mediaRepository, ErrorLogRepository $errorLogRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->userTicketRepository = $userTicketRepository;
        $this->mediaRepository = $mediaRepository;
        $this->err_log = $errorLogRepository;
    }
	//=========================================
	//  基本資料
	//=========================================
    public function check_email_exist($email){
        return $this->userProfileRepository->check_email_exist($email);
    }
	public function get_user($user_id){
    	return $this->userProfileRepository->get_user($user_id);
	}
	public function get_user_id_by_uni_name($user_uni_name){
	    return $this->userProfileRepository->get_user_id_by_uni_name($user_uni_name);
    }
    public function get_user_services($user_id){
		if(!$result = $this->userProfileRepository->get_user_services($user_id)) return ['success' => false];
		$result = $this->get_user_service_names_by_ids(array_pluck($result,'service_id'));
		return ['success' => true, 'data' => $result];
	}
	public function check_user_services($user_id, $services, $service_type){
    	if($service_type == 'string') {
    		$services = $this->get_user_service_id_by_name($services);
		}elseif($service_type != 'int'){
			{$this->err_log->err('wrong service type',self::CLASS_NAME,__FUNCTION__);return ['success' => false];};
		}

		if(!$result = $this->userProfileRepository->get_user_services($user_id)){
			$this->err_log->err('fail to get service',self::CLASS_NAME,__FUNCTION__);return ['success' => false];
		}
		$user_service_ids = array_pluck($result, 'service_id');
		if(is_array($services)){
			foreach ($services as $service){
				if(!in_array($service, $user_service_ids)){$this->err_log->err('no this service: '.$service.';all user service: '.json_encode($user_service_ids),self::CLASS_NAME,__FUNCTION__);return ['success' => false];};
			}
		}else{
			if(!in_array($services, $user_service_ids)){$this->err_log->err('no this service: '.$services.';all user service: '.json_encode($user_service_ids),self::CLASS_NAME,__FUNCTION__);return ['success' => false];};

		}

		return ['success' => true];

	}
	public function update_current_user($data){
        $update = $this->userProfileRepository->update_current_user($data);
        if(!$update){
            throw new CurUserUpdateInfoFail();
        }

        return $update;
    }
	//=========================================
	//  上傳user 大頭貼
	//  分別可透過url 或upload file
	//=========================================
    public function upload_and_set_user_icon($userIconOrgin,$userIcon,$user_id){
        $name = sha1('userIcon'.$user_id.time());
        //upload Media
        $media_id = $this->mediaRepository->upload_media($userIconOrgin, self::User_icon_orginal_path, $name ,$user_id, $userIcon, self::User_icon_path);if(!$media_id['success'])return ['success' => false];
        //記録到user ; 預設會是上傳後立即使用
        if(!$this->userProfileRepository->create_user_icon($user_id, $media_id['media_id'])) return ['success' => false];
        return ['success' => true];

    }

    public function upload_and_set_user_icon_by_url($userIconOrgin_url,$userIcon_url, $user_id){
        $name = sha1('userIcon'.$user_id.time());
        $media_id = $this->mediaRepository->upload_media_by_url($userIconOrgin_url, self::User_icon_orginal_path, $name, $user_id, $userIcon_url, self::User_icon_path);if(!$media_id['success'])return ['success' => false];
        if(!$this->userProfileRepository->create_user_icon($user_id, $media_id['media_id'])) return ['success' => false];
        return ['success' => true];
    }
    //=========================================
    //  上傳user photo大頭貼
    //=========================================
    public function upload_user_photo($photo, $order, $user_id){
        $name = sha1($user_id.time().'user_photo');
        $upload_media = $this->mediaRepository->upload_media($photo,'user/ablums',$name, $user_id);
        DB::beginTransaction();
        if($upload_media['success'] == false) return false;
        $insert_in_user_albums = $this->userProfileRepository->create_or_update_user_photo($upload_media['media_id'],$order,$user_id);
        DB::commit();
        if($insert_in_user_albums['success'] == false) return false;
        return true;

    }
    public function update_user_photo_description($description, $photo_id, $user_id){
        return $this->mediaRepository->update_media_info('media_description',$photo_id,['media_description' => $description], $user_id);
    }

    public function remove_user_photo($media_id, $user_id){
        return $this->userProfileRepository->remove_user_photo($media_id, $user_id);
    }

    public function get_current_use_icon_by_User($user){
        $used_user_icon = null;
        foreach($user->user_icons as $user_icon){
            if($user_icon->is_used == 1) $used_user_icon = $user_icon;
        }
        return $used_user_icon;
    }

	public function get_user_service_type_id($service_name){
		$services = ['us_assistant' => 1, 'us_photographer' => 2, 'us_translator' => 3];

		return isset($services[$service_name]) ? $services[$service_name] : false;
	}

	public function get_user_service_id_by_name($service_names){
		$services = ['us_assistant' => 1, 'us_photographer' => 2, 'us_translator' => 3];
		if(is_array($service_names)){
			//INPUT : array
			$service_ids = array();
			foreach ($service_names as $name){
				array_push($service_ids, $services[$name]);
			}

			return $service_ids;
		}else{
			return $services[$service_names];
		}

	}

	public function get_user_service_names_by_ids($service_ids){
		$services = [1 => 'us_assistant', 2 => 'us_photographer', 3=> 'us_translator'];
		if(is_array($service_ids)) {
			//INPUT : array
			$services_name = array();

			foreach ($service_ids as $id) {
				array_push($services_name, $services[$id]);
			}

			return $services_name;
		}else{
			return $services[$service_ids];
		}
	}
//----------------------------------------------------------------
//       User Ticket
//----------------------------------------------------------------
    public function get_user_activity_tickets($user_id){
        $query = $this->userTicketRepository->get_user_activity_tickets_by_user_id($user_id);
        if(!$query) return null;
        return $query;
    }

    public function get_beneficiary_incidental_tickets($beneficiary_id){
        $query = $this->userTicketRepository->get_ta_ticket_incidental_coupons_by_beneficiary_id($beneficiary_id);
        return $query;
    }

    public function get_beneficiary_incidental_tickets_is_used($beneficiary_id, $days){
        $query = $this->userTicketRepository->get_ta_ticket_incidental_coupons_is_used_in_n_day_by_beneficiary_id($beneficiary_id, $days);
        return $query;
    }
}


