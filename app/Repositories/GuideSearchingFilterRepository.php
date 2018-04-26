<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use hisorange\BrowserDetect\Facade\Parser as BrowserDetect;

class GuideSearchingFilterRepository
{
	/** @var 注入User Model */
	protected $user;

	public function __construct(User $user){
		$this->user = $user;
	}
	/*
	過濾條件
	*/
	public function getFilterResult($input, $travel_area=NULL,$age_range=NULL){
        $service_places = $travel_area['type'] == 'service_places' ? $travel_area['places'] : null;
	    $service_country = $travel_area['type'] == 'service_country' ? $travel_area['places'] : null;
		$user_filter = $this->user_condition_convert($input);
		$guide_filter = $this->guide_condition_convert($input);
		/*Basic Condition*/
		$user =  User::with('guide')->with('trip')->with('review')
                    ->whereNotNull('uni_name')
                    ->whereNotNull('name')
                    ->whereNotIn('name',[''])
                    ->whereNotNull('sex')
                    ->whereNotIn('sex',[''])
                    ->where($user_filter)
                    ->whereNotNull('phone_number')
                    ->whereNotIn('phone_number',['']);
		/*Age Condition*/
		if($age_range == NULL){
		    $user = $user->whereNotNull('birth_date')->whereNotIn('birth_date',['']);
        }else{
            $max_age_date = date('Y-m-d', strtotime('today -'.$age_range[0].' years'));
            $min_age_date = date('Y-m-d', strtotime('today -'.$age_range[1].' years'));
            $user = $user->whereDate('birth_date','>',$max_age_date)->whereDate('birth_date','<',$min_age_date);
        }
        /*User Icon Condition*/
        $user = $user->whereHas('user_icons',function($q){
            $q->where('is_used',1);
        });
		/*guide Condition*/
		$user = $user->whereHas('guide',function($query) use($guide_filter, $service_country, $service_places){
			    if($service_country != null && $service_places == null){
                    $query->where($guide_filter)->whereIn('service_country',$service_country);
                }elseif($service_places != null){
                    $query->whereHas('guideServicePlace',function($q) use($service_places){
                        $q->whereIn('city_name',$service_places);
                    });
                }else{
                    $query->where($guide_filter)->whereNotNull('service_country')->whereNotIn('service_country',['']);
                }
        });
		/*trip Condition*/
		$user = $user->whereHas('trip',function($q){
                $q->where('trip_status','published')->whereHas('trip_media',function($q){
                    if(BrowserDetect::isMobile() == true) {
                        $q->whereNotNull('media_id')->where('media_type', 'img');
                    }else{
                        $q->whereNotNull('media_id');
                    }
                });
            });
         $user =  $user->get();
		return $user;
	}

	private function user_condition_convert($input){
		$result = array();
		foreach ($input as $k => $v) {
			switch($k){
				case 'gender' :
					if($v{'value'} == 'M' || $v{'value'} == 'F')
					array_push($result,['sex',$v{'value'}]);
				break;
				case 'birth_date' :
					array_push($result,['birth_date','<=',$v{'maxValue'}]);
					array_push($result,['birth_date','>=',$v{'minValue'}]);
				break;
			}
		}
		return $result;
	}
	private function guide_condition_convert($input){
		$result = array();
		foreach ($input as $k => $v) {
			switch($k){
				case 'servicePlace' :
					array_push($result,['service_country',$v{'country'}]);
				break;
			}
		}
		return $result;
	}	
	public function sexStrict($request){
		$request->sex ? $sex=$request->sex : $sex = null;
		if(count($sex) == 1){
			switch($sex[0]){
				case 1:
				$sex = 'M';
				break;
				case 2:
				$sex = 'F';
				break;
			}
		}
		return $sex;
	}
	/*交通工具限制*/
	public function transportStrict($request){
		$request->transport ? $transport=$request->transport : $transport = null;		
		if(count($transport) == 1){
			switch($transport[0]){
				case 1:
				$transport = 'has_scooter';
				break;
				case 2:
				$transport = 'has_car';
				break;
			}
		}
		return $transport;
	}


}

?>