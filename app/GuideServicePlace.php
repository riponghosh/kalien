<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuideServicePlace extends Model
{
	protected $table = 'guide_service_places';

	public function service_place(){
		return $this->hasOne('App\ServicePlace','city_name','city_name');
	}
}
?>
