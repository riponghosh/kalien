<?php
function array_group_by($arr, $key)
{
	if (!is_string($key) && !is_int($key) && !is_float($key)) {
		trigger_error('array_group_by(): The key should be a string or an integer', E_USER_ERROR);
	}
// Load the new array, splitting by the target key
	$grouped = [];
	foreach ($arr as $value) {
		$grouped[$value[$key]][] = $value;
	}
// Recursively build a nested grouping if more parameters are supplied
// Each grouped array value is grouped according to the next sequential key
	if (func_num_args() > 2) {
		$args = func_get_args();
		foreach ($grouped as $key => $value) {
			$params = array_merge([$value], array_slice($args, 2, func_num_args()));
			$grouped[$key] = call_user_func_array('array_group_by', $params);
		}
	}
	return $grouped;
}

function object_group_by($obj, $key)
{
	$grouped = [];
	$layer = explode('.',$key);
	foreach ($obj as $k => $v){
		$new_key = $v;
		for($i = 0; $i < count($layer); $i++){
			$new_key = $new_key[$layer[$i]];
		}
		//
		if(!isset($grouped[ $new_key ])){
			$grouped[ $new_key ][0] = $v;
		}else{
			$grouped[ $new_key ][count($grouped[ $new_key ])] = $v;
		}
	}

	return $grouped;
}

function cur_convert($value, $unit, $convert_to_unit = null){
    $unit_group = ['TWD','HKD','JPY','USD'];
	if(!in_array($unit, $unit_group))return null;
    if($convert_to_unit != null){
        if(!in_array($convert_to_unit, $unit_group)) return false;
    }
	$units = ['USD' => 1, 'HKD' => 7.8118, 'TWD' => 30.0291, 'JPY' => 110.1200];
    $cookie_cur_unit = CLIENT_CUR_UNIT;
	$default_unit = $convert_to_unit == null ? $cookie_cur_unit : $convert_to_unit;

	$new_value = $value*$units[$default_unit]/$units[$unit];

	return number_format($new_value,2,'.','');
}

function lan_convert($data, $param){
    $web_lan = isset($_COOKIE['web_language']) ? Cookie::get('web_language') : 'en';
    $lans = ['zh_tw', 'jp', 'en'];

    if($data[$param.'_'.$web_lan] != null) return $data[$param.'_'.$web_lan];
    foreach ($lans as $lan){
        if($data[$param.'_'.$lan] != null) return $data[$param.'_'.$lan];
    }
    return null;

}
//------------------------------------------------
// Date Time
//------------------------------------------------
function get_durations_date_of_special_week($start_date, $end_date, $week_int){
    if(strtotime($end_date) < strtotime($start_date)) return false;
    $output = array();
    $weeks = ['sunday', 'monday', 'tuesday', 'Wednesday','thursday', 'friday', 'saturday'];
    $start_date = Carbon::createFromFormat('Y-m-d', $start_date);
    $end_date = Carbon::createFromFormat('Y-m-d', $end_date);

    $date_of_week  = new Carbon('this '.$weeks[$week_int]);

    strtotime($start_date->toDateString()) > strtotime($date_of_week->toDateString()) ? $date_of_week->addWeek() : $date_of_week;
    while(strtotime($date_of_week->toDateString()) < strtotime($end_date->toDateString())){
        array_push($output, $date_of_week->toDateString());
        $date_of_week->addWeek();
    }
    return $output;

}
//------------------------------------------------
// Pneko Fee
//------------------------------------------------
function pneko_fee($amount, $pneko_fee_percentage, $amount_unit){
    $fee = cur_convert($amount*$pneko_fee_percentage*0.01,$amount_unit, $amount_unit);
    $minimum_pneko_fee = cur_convert(env('PNEKO_MINIMUM_FEE'),  env('PNEKO_MINIMUM_FEE_UNIT'), $amount_unit);
    $fee = $fee >= $minimum_pneko_fee ? $fee : $minimum_pneko_fee;
    return $fee;
}

function storageUrl($path){
    if(empty($path)) return '';
    return Storage::url($path);
}
?>