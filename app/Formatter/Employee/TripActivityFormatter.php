<?php

namespace App\Formatter\Employee;

use App\Formatter\Interfaces\IFormatter;

class TripActivityFormatter implements IFormatter
{

    protected $tripActivityTicketsFormatter;
    function __construct(TripActivityTicketsFormatter $tripActivityTicketsFormatter)
    {
        $this->tripActivityTicketsFormatter = $tripActivityTicketsFormatter;
    }

    function dataFormat($tripActivity, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$tripActivity)
        {
            return [];
        }

        $tripActivity = $this->activity_language_convert($tripActivity, 'zh_tw');
        return (object) [
            'activity_page_url' => env('APP_URL').'/activity/'.$tripActivity->uni_name,
            'open_time' => $tripActivity->open_time,
            'close_time' => $tripActivity->close_time,
            'title' => $tripActivity->title,
            "sub_title" => $tripActivity->sub_title,
            "description" => $tripActivity->description,
            "map_address" => $tripActivity->map_address,
            "tel" => $tripActivity->tel,
            "tel_area_code" => $tripActivity->tel_area_code,
            "time_zone" => $tripActivity->time_zone,
            "uni_name" => $tripActivity->uni_name,
            "map_url" => $tripActivity->map_url,
            "refund_rules" => $tripActivity->trip_activity_refund_rules,
            'trip_activity_tickets' => $this->tripActivityTicketsFormatter->dataFormat($tripActivity->trip_activity_tickets)
        ];
    }

    public function activity_language_convert($data, $language){
        $lan = ['zh_tw', 'jp', 'en'];
        $trans_data = ['title','sub_title','description','map_address'];
        if(!in_array($language, $lan)) return false;
        foreach ($trans_data as $k){
            if($data{$k.'_'.$language} != null){
                $data{$k} = $data{$k.'_'.$language};
            }else{
                //取得唯一語言
                $lan_detect = $lan;
                foreach ($lan_detect as $det_lan){
                    if($data{$k.'_'.$det_lan} != null){
                        $data{$k} = $data{$k.'_'.$det_lan};
                        break ;
                    }
                    if(end($lan_detect) === $det_lan){
                        $data{$k} = null;
                    }
                }
            }


        }
        return $data;
    }
}