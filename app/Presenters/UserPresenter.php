<?php
namespace App\Presenters;

class UserPresenter{

	public function language($language_name){
		$name = trans('languages.'.$language_name);
		return $name;	
	}
	public function birth_date_convert_to_age($birth_date){
		$age = floor((time() - strtotime($birth_date)) / 31556926);
		return $age;
	}
	public function create_phone_area_code_option($default_code=null){
		$group = null;
		$area_codes = array(
			['area' => 'tw','code' => 886],
            ['area' => 'hk','code' => 852],
			['area' => 'mo','code' => 853],
			['area' => 'jp','code' => 81]
		);
		foreach($area_codes as $k){
			$select = $default_code == $k{'code'} ? 'selected' : null;
            $option = '<option value="'.$k{'code'}.'" '.$select.'>'.'('.$k{'area'}.') '.$k{'code'}.'</option>';
			$group = $group.$option;
		}
		echo $group;
	}
	public function get_user_service_names($service_ids){
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

	public function cur_units_list(){
		return ['HKD'=> 'HKD','TWD' => 'TWD', 'JPY' => 'JPY'];
	}
	public function cur_units($unit ,$att){
        $data = [
        	'HKD' => ['s' => 'HK$'],
			'TWD' => ['s' => 'NT$'],
			'JPY' => ['s' => 'JP¥']
		];
        return $data[$unit][$att];
	}
}
?>
