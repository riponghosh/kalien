<?php
namespace  App\Formatter\TripActivity;

use App\Formatter\Interfaces\IFormatter;
use App\Formatter\TripActivity\TripActivityTicketsFormatter;

class TripActivityFormatter implements IFormatter
{
    protected $tripActivityTicketsFormatter;
    protected $productImagesFormatter;

    public function __construct(TripActivityTicketsFormatter $tripActivityTicketsFormatter, ProductImagesFormatter $productImagesFormatter)
    {
        $this->productImagesFormatter = $productImagesFormatter;
        $this->tripActivityTicketsFormatter = $tripActivityTicketsFormatter;
    }

    public function dataFormat($tripActivity, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$tripActivity)
        {
            return [];
        }

        $tripActivity = $this->activity_language_convert($tripActivity, app()->getLocale());
            return [
                'activity_page_url' => env('APP_URL').'/activity/'.$tripActivity->uni_name,
                'open_time' => $tripActivity->open_time,
                'close_time' => $tripActivity->close_time,
                'title' => $tripActivity->title,
                "sub_title" => $tripActivity->sub_title,
                "description" => $tripActivity->description,
                "map_address" => $tripActivity->map_address,
                "merchant_id" => $tripActivity->merchant_id,
                "tel" => $tripActivity->tel,
                "tel_area_code" => $tripActivity->tel_area_code,
                "time_zone" => $tripActivity->time_zone,
                "uni_name" => $tripActivity->uni_name,
                "map_url" => $tripActivity->map_url,
                "refund_rules" => optional($tripActivity->trip_activity_refund_rules),
                "trip_gallery_pic" => storageUrl(!empty($tripActivity->trip_img->where('is_gallery_image',1)->first()) ? $tripActivity->trip_img->where('is_gallery_image',1)->first()->media->media_location_standard : null),
                "trip_gallery_pic_low_quality" => !empty($tripActivity->trip_img->where('is_gallery_image',1)->first()) ? $tripActivity->trip_img->where('is_gallery_image',1)->first()->media->media_location_low :null,
                "media" => $this->productImagesFormatter->dataFormat($tripActivity->trip_img),
                "rule_infos" => $tripActivity->rule_infos,
                "trip_activity_short_intros" => $tripActivity->trip_activity_short_intros,
                "trip_activity_refund_rules" => $tripActivity->trip_activity_refund_rules,
                "customer_rights" => $tripActivity->customer_rights,
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