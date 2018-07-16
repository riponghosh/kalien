<?php
namespace App\Presenters;
use Illuminate\Support\Facades\File;
use App\Json;
use Illuminate\Support\Facades\Storage;

class CountryPresenter
{
	/*國家iso資料*/
	protected $country_iso_data;
	/*國家與省份*/
	protected $tw_city_data;
	protected $jp_city_data;
	function __construct(){
		$country_iso = File::get("../database/data/country-iso-numeric.json");
		$this->country_iso_data  = json_decode($country_iso, true);
		$this->tw_city_data = json_decode(file_get_contents(base_path("/database/data/taiwan-country-city.json")),true);
        $this->jp_city_data = json_decode(file_get_contents(base_path("database/data/japan-country-city.json")),true);
	}
	public function create_country_option($is_existed = null){
		$group = null;
		foreach ($this->country_iso_data as $data) {
			if($data{'iso'} == $is_existed){
				$option = '<option value="'.$data{'iso'}.'" selected>'.$data{'country'}.'</option>';
			}else{
				$option = '<option value="'.$data{'iso'}.'">'.$data{'country'}.'</option>';
			};
			$group = $group.$option;
		}
		echo $group;
	}
	
	public function iso_convertTo_name($iso_code){
		print_r(array_search($iso_code,array_column($this->country_iso_data, 'iso','country') ) );
	}
	public function get_city($country){
		switch ($country) {
			case 'tw':
				return $this->get_tw_city();
				break;
            case 'jp':
                return $this->get_jp_city();
                break;
			default:
				return;
				break;
		};
	}
	/*輸出台灣各省份陣列*/
	public function get_tw_city(){
		$output = array();
		foreach($this->tw_city_data as $data){
			array_push($output,$data{'city_name'});
		}
		return $output;
	}
    public function get_jp_city(){
        $output = array();
        foreach($this->jp_city_data as $data){
            array_push($output,$data{'city_name'});
        }
        return $output;
    }
	public function service_country_name($country){
		switch($country){
			case 'tw':
				return trans('countries.Taiwan');
			break;
			case 'kp':
				return trans('countries.Korea');
			break;
			case 'hk':
				return trans('countries.HongKong');
			break;
			case 'jp':
				return trans('countries.Japan');
			break;
			case 'mo':
				return trans('countries.Macau');
			break;
		}
	}

}
?>
