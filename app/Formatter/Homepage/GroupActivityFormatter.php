<?php

namespace App\Formatter\Homepage;

use App\Formatter\Interfaces\IFormatter;

class GroupActivityFormatter implements IFormatter
{
    protected $participantsFormatter;
    protected $hostFormatter;
    function __construct(HostFormatter $hostFormatter, ParticipantsFormatter $participantsFormatter)
    {
        $this->participantsFormatter = $participantsFormatter;
        $this->hostFormatter = $hostFormatter;
    }

    function dataFormat($group_activity, callable $closure = null)
    {
        if(!$group_activity) return [];

        return [
            'gp_activity_id' => $group_activity['gp_activity_id'],
            'product_name' => $group_activity['trip_activity_ticket']['Trip_activity']['title_zh_tw'],
            'product_gallery_image' => storageUrl(!empty($group_activity['trip_activity_ticket']['Trip_activity']->trip_img->where('is_gallery_image',1)->first()) ? $group_activity['trip_activity_ticket']['Trip_activity']->trip_img->where('is_gallery_image',1)->first()->media->media_location_standard : null),
            'product_image' => '',
            'price' => $group_activity['trip_activity_ticket']['amount'],
            'price_unit' => $group_activity['trip_activity_ticket']['currency_unit'],
            'start_datetime' => $group_activity['start_at'],
            'location' => $group_activity['trip_activity_ticket']['trip_activity']['map_address_zh_tw'],
            'tz' => $group_activity['timezone'],
            'activity_title' => $group_activity['activity_title'],
            'participants' => $this->participantsFormatter->dataFormat($group_activity['applicants']),
            'host' => $this->hostFormatter->dataFormat($group_activity->host)
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