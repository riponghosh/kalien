<?php
namespace App\Presenters;

use Illuminate\Support\Facades\Cookie;

class TripActivityPresenter{
    public function lan_convert($data, $param){
        $web_lan = Cookie::get('web_language');
        $lans = ['zh_tw', 'jp', 'en'];

        if($data[$param.'_'.$web_lan] != null) return $data[$param.'_'.$web_lan];
        foreach ($lans as $lan){
            if($data[$param.'_'.$lan] != null) return $data[$param.'_'.$lan];
        }
        return null;

    }

    public function activity_rule($rule, $params){
        $trans_params = array();
        if($params == null){
            return trans('tripActivityInfo.'.$rule);
        }
        if(!is_array($params)){
            $trans_params['i0'] = $params;
        }else{
            foreach ($params as $k => $param){
                $trans_params['i'.$k] = $param;
            }
        }
        return trans('tripActivityInfo.'.$rule, $trans_params);
    }

    public function activity_rule_icon($rule){
        if($rule == 'age_min' || $rule == 'age_min'){
            return 'user-o';
        }elseif($rule == 'group_participant_min' || $rule == 'group_participant_max' || $rule == 'group_participant_between'){
            return 'users';
        }elseif($rule == 'refund_before_n_day' || $rule == 'refund_forbidden'){
            return 'dollar';
        }

        return true;
    }
}
