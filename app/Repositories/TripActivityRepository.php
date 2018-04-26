<?php

namespace App\Repositories;

use App\TaTicketIncidentalCoupon;
use App\Product;
use App\TripActivityMedia;
use App\Media;
use App\Repositories\ErrorLogRepository;
use App\TripActivityTicket;
use Illuminate\Support\Facades\DB;


class TripActivityRepository
{
    const CLASS_NAME = 'TripActivityRepository';
    protected $tripActivity;
    protected $tripActivityMedia;
    protected $tripActivityTicket;
    protected $taTicketIncidentalCoupon;
    protected $media;
    protected $err_log;

    public function __construct(Product $tripActivity, TaTicketIncidentalCoupon $taTicketIncidentalCoupon, TripActivityMedia $tripActivityMedia, TripActivityTicket $tripActivityTicket, Media $media, ErrorLogRepository $errorLogRepository)
    {
        $this->tripActivity = $tripActivity;
        $this->tripActivityMedia = $tripActivityMedia;
        $this->tripActivityTicket = $tripActivityTicket;
        $this->taTicketIncidentalCoupon = $taTicketIncidentalCoupon;
        $this->err_log = $errorLogRepository;
        $this->media = $media;
    }

    public function get_trip_activity($get_by,$lan,$type){
        if($type != 'uni_name' && $type != 'id') return false;
        $data = $this->tripActivity->where($type, $get_by)->first();
        if(!$data) return false;
        $data = $this->activity_language_convert($data, $lan);
        /*寫入gallery*/
        if(isset($data->trip_img[0])){
            foreach($data->trip_img as $trip_img){
                /*取video*/
                if($trip_img->media_type == 'video_url'){
                    $data->video_url = $trip_img->media->media_location_standard;
                }
                /*取banner*/
                if($trip_img->is_gallery_image == 1){
                    $data->trip_gallery_pic = $trip_img->media->media_location_standard;
                    $data->trip_gallery_pic_id = $trip_img->id;
                }
            }
        }else{
            $data['trip_gallery_pic'] = '';
        }
        return $data;
    }

    public function get_trip_activity_ticket_by_activity_id($activity_id, $lan, $available=true){
        $query = $this->tripActivityTicket->where('available', $available)->where('trip_activity_id',$activity_id)->get();
        if(!$query) return null;
        if(count($query) == 0) return null;
        $data = array();
        foreach ($query as $d){
            array_push($data,$this->activity_ticket_language_convert($d, $lan));
        }
        return $data;
    }

    public function get_activity_ticket_incidental_coupon($ticket_id){
        $query = $this->taTicketIncidentalCoupon->where('trip_activity_ticket_id', $ticket_id)->first();
        return $query;
    }

    public function get_trip_activities($num, $lan){
        $result = $this->tripActivity->limit($num)->get();
        $outputs = array();
        foreach($result as $data){
            /*寫入gallery*/
            $data = $this->activity_language_convert($data, $lan);
            if(isset($data->trip_img[0])){
                foreach($data->trip_img as $trip_img){
                    /*取video*/
                    if($trip_img->media_type == 'video_url'){
                        $data->video_url = $trip_img->media->media_location_standard;
                    }
                    /*取banner*/
                    if($trip_img->is_gallery_image == 1){
                        $data->trip_gallery_pic = $trip_img->media->media_location_standard;
                        $data->trip_gallery_pic_id = $trip_img->id;
                    }
                }
            }else{
                $data['trip_gallery_pic'] = '';
            }
            array_push($outputs,$data);
        }
        return $result;
    }
    public function update_trip_activity($id, $language, $datas){
        $update = $this->tripActivity->findOrFail($id)->update($datas);
        return $update;
    }
//-------------------------------------------------------------
//  Trip Activity Img
//-------------------------------------------------------------
    public function create_trip_image($id, $image_id){
            /*新增image*/
            $insert_query = $this->tripActivityMedia->create([
                'trip_activity_id' => $id,
                'media_id' => $image_id,
                'is_gallery_image' => 0
            ]);
            DB::commit();
            if(!$insert_query) return false;

        return true;
    }

    public function update_trip_gallery_image($id, $image_id){
        DB::beginTransaction();
        /*soft delete 現有gallery*/
        $get_media = $this->tripActivityMedia->where('is_gallery_image',1)->where('trip_activity_id',$id)->get();
        if($get_media){
            if(count($get_media) > 1){
                return false;
            } elseif(count($get_media) == 1){
                $this->tripActivityMedia->where('is_gallery_image',1)->where('trip_activity_id',$id)->delete();
                $this->media->where('media_id',$get_media[0]->media_id)->delete();
            }
        }
        /*新增image*/
        $insert_query = $this->tripActivityMedia->create([
            'trip_activity_id' => $id,
            'media_id' => $image_id,
            'is_gallery_image' => 1
        ]);
        DB::commit();
        if(!$insert_query) return false;
        return true;
    }

    public function update_trip_activity_video_url($id, $video_url, $user_id){
        //只有一條影片時的方法
        $check_media_exist = $this->tripActivityMedia->where('trip_activity_id',$id)->where('media_type','video_url')->first();
        if($check_media_exist){
            if($check_media_exist->media->media_location_standard == $video_url){
                $this->err_log->err('same media url',self::CLASS_NAME, __FUNCTION__);
                return false;
            }
        }
        DB::beginTransaction();
        $insert_media = Media::create([
            'media_author' => $user_id,
            'media_location_standard' => $video_url,
            'media_format' => 'url'
        ]);
        if(!$insert_media) return false;
        $insert_trip_activity = $this->tripActivityMedia->updateOrCreate(
            ['trip_activity_id' => $id,'media_type' => 'video_url'],
            ['media_id' => $insert_media->media_id]
        );
        DB::commit();
        if(!$insert_trip_activity) return false;
    }
    public function remove_trip_activity_video_url($id){
        //只有一條影片時的方法
        DB::beginTransaction();
        $check_media_exist = $this->tripActivityMedia->where('trip_activity_id',$id)->where('media_type','video_url')->first();
        if($check_media_exist){
            $this->media->where('media_id',$check_media_exist->media_id)->delete();
            $this->tripActivityMedia->where('id',$check_media_exist->id)->delete();
        }
        DB::commit();
        return true;
    }
    public function delete_trip_gallery_image($trip_id, $trip_gallery_id){
        DB::beginTransaction();
            $get_media_id = $this->tripActivityMedia->where('trip_activity_id', $trip_id)->where('id',$trip_gallery_id)->first();
            if(!$get_media_id) return ['success' => false, 'msg' => 'gallery not exist'];
            $delete_image = $this->media->where('media_id',$get_media_id->media_id)->delete();
            if(!$delete_image) return ['success' => false, 'msg' => 'delete media fail'];
            $delete_gallery = $this->tripActivityMedia->where('trip_activity_id', $trip_id)->where('id',$trip_gallery_id)->delete();
             if(!$delete_gallery) return ['success' => false, 'msg' => 'delete gallery fail'];
        DB::commit();
        return ['success' => true];
    }

    public function update_trip_activity_media_info($trip_id, $trip_img_id, $attr = array()){
        $trip_img = $this->tripActivityMedia->where('trip_activity_id',$trip_id)->where('id', $trip_img_id)->first();
        if(!$trip_img){
            return ['success' => false];
        }
        $trip_img_update = $trip_img->update($attr);
        if(!$trip_img_update){
            return ['success' => false];
        }

        return ['success' => true];
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

    public function activity_ticket_language_convert($data, $language){
        $lan = ['zh_tw', 'jp', 'en'];
        $trans_data = ['name','description'];
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
?>

