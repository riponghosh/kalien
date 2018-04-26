<?php
namespace App\Presenters;
use Carbon\Carbon;

class DatePresenter
{
	function Date_time_to_timeStamp($date,$time){
		$dt = Carbon::parse($date.' '.$time.':00');
		return $dt->timestamp;
	}
	function time_to_min($time){
		$new_time = explode(":",$time);
		return $new_time[0]*60 + $new_time[1];
	}
	public function get_min_and_max_date($dates){
		if(gettype($dates) != 'array') return null;
		if(count($dates) == 0) return null;
        for ($i = 0; $i < count($dates); $i++){
            if ($i == 0){
                $max_date = date('Y-m-d H:i:s', strtotime($dates[$i]));
                $min_date = date('Y-m-d H:i:s', strtotime($dates[$i]));
            }elseif ($i != 0){
                $new_date = date('Y-m-d H:i:s', strtotime($dates[$i]));
                if ($new_date > $max_date)
                {
                    $max_date = $new_date;
                }
                else if ($new_date < $min_date)
                {
                    $min_date = $new_date;
                }
            }
		}
		return [
			'max_date' => date('Y-m-d',strtotime($max_date)),
			'min_date' => date('Y-m-d',strtotime($min_date)),
			'amount'   => count($dates)
		];

	}
}
?>
