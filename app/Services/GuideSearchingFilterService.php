<?php
namespace App\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Http\Requests;

class GuideSearchingFilterService
{
	public $tw_region_arr;

	public function __construct()
	{ 
		$tw_region_arr = [];
		$tw_city_data = json_decode(File::get("../database/data/taiwan-country-city.json"),true);
		foreach ($tw_city_data as $value) {
			array_push($tw_region_arr,$value{'city_name'});
		}
		$this->tw_region_arr = $tw_region_arr; 
	}
	/*
	**輸入國家，地區（可多個）；輸出一各個省份
	*/
	public function servicePlace_region_validator($country,$regions){
		switch($country){
			case 'tw':
			$output = array();
			foreach ($regions as $region) {
				in_array($region,$this->tw_region_arr);
				array_push($output,$region);
			}
			break;
			default:
				return false;
		}
		return $output;
	}
	/*
	** 輸入區域，如台北，輸出多個城市（台北市，新北市，基隆市...）
    */
	public function get_cities_by_region_name($region){
	    $service_places = 'service_places';
	    $service_country = 'service_country';
	    switch($region){
            case 'taipei':
                $type = $service_places;
                $result = ['Taipei_City','Newtaipei_City','Keelung_City','Yilan_County','Taoyuan_City'];
                break;
            case 'tainan_kaohsiung':
                $type = $service_places;
                $result = ['Tainan_City','Kaohsiung_City'];
                break;
            case 'taichung':
                $type = $service_places;
                $result = ['Taichung_City','Miaoli_County','Hsinchu_City','Hsinchu_County'];
                break;
            case 'hongkong':
                $type = $service_country;
                $result = ['hk'];
                break;
            case 'macau':
                $type = $service_country;
                $result = ['mo'];
                break;
            case 'hongkong_macau':
                $type = $service_country;
                $result = ['hk','mo'];
                break;
            default:
                $type = '';
                $result = [];
        }
        return $output = ['type' => $type, 'places' => $result];
    }
}
?>