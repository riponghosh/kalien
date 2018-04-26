<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ServicePlacesTableSeeder extends Seeder
{
	public $tw_city_data;
	public $kp_city_data;
	public $jp_city_data;
	public $city_data;
	function __construct(){
		$this->tw_city_data = json_decode(File::get("database/data/taiwan-country-city.json"),true);
		$this->kp_city_data = json_decode(File::get("database/data/korea-country-city.json"),true);
        $this->jp_city_data = json_decode(File::get("database/data/japan-country-city.json"),true);
		$data = array();
		foreach($this->tw_city_data as $d){
			array_push($data,$d);
		}
		foreach($this->kp_city_data as $d){
			array_push($data,$d);
		}
        foreach($this->jp_city_data as $d){
            array_push($data,$d);
        }
		$this->city_data = $data;
	}
	public function run()
	{
		DB::table('service_places')->truncate();
		DB::table('service_places')->insert(
			$this->city_data
		);
	}
}
?>
