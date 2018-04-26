<?php
namespace App\Repositories;

use App\User;
use App\UserAlbum;
use App\UserIcon;
use App\UserIntroVideo;
use App\Guide;
use App\Media;
use App\UserReview;
use App\UserLanguage;
use App\UserService;
use Carbon\Carbon;
use App\GuideTouristMatch;
use App\GuideServicePlace;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class UserProfileRepository{
	/** @var 注入User,Review Model */
	protected $user;
	protected $userAlbum;
	protected $userReview;
	protected $userService;
	protected $guide;
	protected $media;
	protected $guideTouristMatch;
	protected $mediaRepository;
	/*json file*/
	protected $tw_city_data;

	public function __construct(User $user, UserAlbum $userAlbum, UserReview $userReview, UserService $userService, Guide $guide, Media $media, GuideTouristMatch $guideTouristMatch, MediaRepository $mediaRepository){
		$this->user = $user;
		$this->userAlbum = $userAlbum;
		$this->userReview = $userReview;
		$this->userService = $userService;
		$this->guide = $guide;
		$this->media = $media;
		$this->guideTouristMatch = $guideTouristMatch;
		$this->mediaRepository = $mediaRepository;
		$this->tw_city_data = json_decode(File::get("../database/data/taiwan-country-city.json"),true);
        $this->jp_city_data = json_decode(File::get("../database/data/japan-country-city.json"),true);

	}

	public function check_email_exist($email){
	    $query = $this->user->where('email',$email)->first();
	    return $query;
    }
	public function get_user_id_by_uni_name($uni_name){
	    $query = User::where('uni_name',$uni_name)->first();
	    return $query->id;
    }
	/********************************************************
	|Current User
	*******************************************************/
	public function get_current_user(){
		return Auth::user();
	}
	public function get_current_user_albums(){
         $get_datas = $this->userAlbum->where('user_id',Auth::user()->id)->get();
         $output = array();
         $count = array();
        if($get_datas) {
            foreach ($get_datas as $data) {
                $photo = array();
                $photo['media_path'] = $data->media->media_location_standard;
                $photo['media_id'] = $data->media_id;
                $photo['order'] = $data->order;
                $photo['media_description'] = $data->media->media_description;

                $output[$data->order] = $photo;
                array_push($count, $data->order);
            }
        }
         for($i = 1;$i<=5; $i++){
             if(!in_array($i, $count)) {
                 $photo = ['media_path' => null, 'media_id' => null, 'order' => $i,'media_description' => null];
                 $output[$i] = $photo;
             }


         }
         return $output;
    }
    public function get_user_albums($user_id, $num){
        $get_datas = $this->userAlbum->where('user_id',$user_id)->orderBy('order', 'desc')->limit($num)->get();
        $output = array();
        if(!$get_datas) return null;
        if(count($get_datas) == 0) return null;
        foreach($get_datas as $data){
            $photo = array();
            $photo['media_path'] = $data->media->media_location_standard;
            array_push($output,$photo);

        }
        return $output;
    }

    public function get_user_services($user_id){
    	$get_query = $this->userService->where('user_id', $user_id)->get();

    	return $get_query;
	}
	public function update_current_user($data){
	    $user = array();
	    foreach($data as $k => $v){
	        if($v != "" && $v != null){
	            $user[$k] = $v;
            }
        }
        if(isset($user['intro_video'])){
            $media_id = $this->mediaRepository->insert_url($user['intro_video'],Auth::user()->id);
            if(!$media_id) return false;
            UserIntroVideo::updateOrCreate(['user_id'=>Auth::user()->id],['media_id' => $media_id,'media_type' => 'video_url']);
        }
		return $query = Auth::user()->update($user);

	}

	public function create_current_guide(){
		DB::beginTransaction();
			Auth::user()->guide()->firstOrCreate(['user_id'=>Auth::user()->id]);
			Auth::user()->guide()->update(['status' => 1]);
		DB::commit();
	}
	/*
	**User
	*/
	public function get_user($user_id){
		return  User::with('guide')->with('user_icons')
			 		->where('id',$user_id)
			 		->first();
	}
	public function getReviewer($userId){
		return $this->userReview
					->where('user_id','=',$userId)
					->get();
	}
	/*
	**Guide
	*/
	public function get_guide_match_status($guide_id,$tourist_id){
        
		$match_status = (object)array('status' => null,'request_url' => null);

		if($tourist_id == null){
			$match_status->status = 0;
			$match_status->request_url = '/send_appointment_request_to_guide';

			return $match_status;
		}
		$query = $this->guideTouristMatch
                      ->select('status')
                      ->where('guide_id','=',$guide_id)
                      ->where('tourist_id',$tourist_id)
                      ->get();
		/*status : 0 沒有預約； 1: 交易進行中； 2: 等待接受請求*/
		if($query == '[]'){
			$match_status->status = 0;
			$match_status->request_url = '/send_appointment_request_to_guide';
		}else{
			if($query[0]->status == 0){
				$match_status->status = 0;
				$match_status->request_url = '/send_appointment_request_to_guide';
			}elseif($query[0]->status == 1){
				$match_status->status = 1;
				/*轉至排程表*/
				$match_status->request_url = '';
			}elseif($query[0]->status==2){
				$match_status->status = 2;
				$match_status->request_url = '/cancel_appointment_for_guide';
			}else{
				$match_status->status = 0;
				$match_status->request_url = '/send_appointment_request_to_guide';
			}
		};

		return $match_status;
	}
	public function get_user_language($user_id){
		$query =  UserLanguage::where('user_id',$user_id)->get();
		return $query;
	}
	/*
	*Edit UserProfile
	*/
	public function update_user_language($datas,$user_id){
		$language = [];
		$query_del = DB::table('user_languages')->where('user_id' ,$user_id)->delete();
		foreach ($datas as $data){
			$uniq = $user_id.':'.$data['language_id'];
			//insert method & query one time
			$language[] = [
					'uniq' 			=> $uniq,
					'user_id'		=> $user_id,
					'language_id'	=> $data['language_id'],
					'level'			=> $data['level']
			];
		}
		UserLanguage::insert($language);
	}

	public function update_user_service($all_data, $user_id){
		$services = array();
		$query_del = $this->userService->where('user_id', $user_id)->delete();
		foreach ($all_data as $data){
			$service_id = $this->get_user_service_type_id($data);
			if($service_id == false) return ['success' => false];
			$services[] = [
				'user_id' => $user_id,
				'service_id' => $service_id
			];
		}
		$insert = $this->userService->insert($services);

		if(!$insert) return ['success' => false];
		return ['success' => true];

	}
    /*
     * Update Guide
     */
    public function update_current_guide($charge_per_day = null, $currency_unit = null){
        $data = array();
        if($charge_per_day != null) $data['charge_per_day'] = $charge_per_day;
        if(!$charge_per_day != null) $data['currency_unit'] = $currency_unit;
        $query = Guide::where('user_id',Auth::user()->id)->update($data);
        if(!$query) return false;
        return true;
    }
	/*
	 * Update Guide Status
	 */
	public function get_current_guide_status(){
        $query = Guide::where('user_id',Auth::user()->id)->first();
        if(!$query) return false;
        return $query['status'];
    }
	public function update_current_guide_status($status){
	    if($status == 'true') {
            if(!Auth::user()->guide()->updateOrCreate(['user_id' => Auth::user()->id], ['status' => 1])) return ['success' => false];
        }elseif($status == 'false'){
            if(!Auth::user()->guide()->updateOrCreate(['user_id' => Auth::user()->id], ['status' => 0]) ) return ['success' => false];
        }
        return ['success' => true];
    }

	public function create_user_icon($user_id,$media_id,$is_used = true){
        DB::beginTransaction();
            UserIcon::where('user_id',$user_id)->update(['is_used' => 0]);
            $query = UserIcon::create([
                'user_id' => $user_id,
                'media_id' => $media_id,
                'is_used' => true
            ]);
        DB::commit();
        if(!$query) return false;
        return true;
	}
	public function update_guide_service_places($country,$cities){
		$datas = [];
		/*驗證*/
		$service_place_validator = $this->guide_service_places_validator($country,$cities);
		if($service_place_validator['success'] == false) return $service_place_validator;
		/*Data處理*/
		if(isset($cities)){
			foreach($cities as $city){
				$datas[] = array(
					'guide_id'	   => Auth::user()->id,
					'country_name' => $country,
					'city_name'	   => $city
				);
			} 
		}
		DB::beginTransaction();
			Auth::user()->guide()->update(['service_country'=>$country]);
			$del_query = GuideServicePlace::where('guide_id',Auth::user()->id)->delete();
			$query =  GuideServicePlace::insert($datas);
		DB::commit();
		if(!$query) return ['success' => false];
		return ['success' => true];

	}
	/*
	 * Photo Ablums
	 */
	public function create_or_update_user_photo($photo_id, $order, $user_id){
        $insert = $this->userAlbum->updateOrCreate(
            ['user_id' => $user_id,'order' => $order],
            ['media_id' => $photo_id]
        );
        if(!$insert) return ['success' => false];
        return ['success' => true];
    }
    public function remove_user_photo($media_id, $user_id){
        DB::beginTransaction();
            $remove_in_album = $this->userAlbum->where('user_id',$user_id)->where('media_id',$media_id)->delete();
            $remove_media = $this->media->where('media_author',$user_id)->where('media_id',$media_id)->delete();
        DB::commit();

        return true;
    }
    private function get_user_service_type_id($service_name){
		$services = ['us_assistant' => 1, 'us_photographer' => 2, 'us_translator' => 3];

		return isset($services[$service_name]) ? $services[$service_name] : false;
	}
	private function guide_service_places_validator($country,$cities){
	    $error_1 = ['success' => false,'msg' => 'cannot without city'];
	    $error_2 = ['success' => false, 'msg' => 'country does not exist'];
	    $success = ['success' => true];
		switch($country){
			case 'tw':
				/*預設資料放入array*/
				$tw_city = array();
				foreach ($this->tw_city_data as $data) {
					array_push($tw_city,$data{'city_name'});
				}
				/*比對*/
				if($cities == null) return $error_1;
				foreach($cities as $city){
					if(!in_array($city,$tw_city))return $error_1;
				}
				return $success;
				break;
			case 'jp':
                if($cities == null) return $error_1;
                $jp_city = array();
                foreach ($this->jp_city_data as $data) {
                    array_push($jp_city,$data{'city_name'});
                }
                /*比對*/
                foreach($cities as $city){
                    if(!in_array($city,$jp_city))return $error_1;
                }
                return $success;
				break;
			case 'kp':
					if(count($cities) > 0) return false;
					return $success;
				break;
			case 'hk':
					if(count($cities) > 0) return false;
					return $success;
				break;
			case 'mo':
					if(count($cities) > 0) return false;
					return $success;
				break;
			default:
				return $error_2;
		}
	}
	
}
?>