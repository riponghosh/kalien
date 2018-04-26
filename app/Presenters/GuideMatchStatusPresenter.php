<?php
namespace App\Presenters;

class GuideMatchStatusPresenter
{

	public function convert_status_to_string($status){

		switch($status){		
		case 0 :
		 $string = '發送同遊請求';
		break;
		case 1 :
		 $string = '對談中';
		break;
		case 2 :
		 $string = '取消請求';
		 break; 
		};
		return $string;

	}
}
?>