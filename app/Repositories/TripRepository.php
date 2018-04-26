<?php

namespace App\Repositories;
use App\Trip;
use App\Media;
use App\TripMedia;
use hisorange\BrowserDetect\Facade\Parser as BrowserDetect;

class TripRepository
{
    public $trip;
    function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }
    function get_current_all_trip($user_id){
        $trip = $this->trip;
        $result = $trip->where('trip_author',$user_id)->get();
        return $result;
    }
    function get_all_trip($user_id, $allow_publish = false){
        /*allow_publish = true : 只回傳通過審查的行程，如要有topic,image等*/
        if($allow_publish == false) {
            $trip = $this->trip;
            $result = $trip->where('trip_author', $user_id)->where('trip_status', 'published')->get();
        }else{
            $trip = $this->trip;
            $result = $trip->where('trip_author', $user_id)
                ->whereNotNull('trip_title')->where('trip_status', 'published')
                ->whereHas('trip_media',function($q){
                    if(BrowserDetect::isMobile() == true){
                       $q->where('media_type','img');
                    }else {
                        $q->whereIn('media_type', ['img', 'video_url']);
                    }
                })->get();
        }
        return $result;
    }
    function add_trip($user_id, $publish){
        $trip_num_row = Trip::where('trip_author',$user_id)->count();
        if($trip_num_row >= 5) return false;
        $result = Trip::create(['trip_author' => $user_id, 'trip_status' => $publish]);
        if(!$result) return false;
        /*Laravel 自動把trip_id轉成id*/
        return $result;
    }
    function del_trip($trip_id, $user_id){
        $result = Trip::where('trip_id',$trip_id)->where('trip_author',$user_id)->delete();
        return true;
    }
    function update_trip_status($trip_id, $status){
        if($status == 'true'){
            $status = 'published';
        }elseif($status == 'false'){
            $status = 'draft';
        }else{
            return false;
        }
        $result = Trip::where('trip_id',$trip_id)->update(['trip_status' => $status]);
        return $result;
    }
    function update_trip($trip_id, $user_id, $data){
        return Trip::where('trip_id',$trip_id)->where('trip_author',$user_id)->update($data);

    }
    public function upload_trip_media($trip_id, $media_id, $user_id, $order){
        if($order != 1)return ['success' => false , 'msg' => 'no this order'];
        /*--
        * 1. 寫入media_trip
         --*/
        //Check trip 存在
        $query = Trip::where('trip_id',$trip_id)->where('trip_author',$user_id)->first();
        if(!$query) return ['success' => false, 'msg' => 'no match trip'];
        //Important ! 只更feature_order =>1 為主圖，要考慮未來擴充1trip多圖問題
        $query = TripMedia::updateOrCreate(
            ['trip_id' => $trip_id, 'feature_order' => $order,'media_type' => 'img'],
            ['media_id' => $media_id]
        );
        if(!$query) return ['success' => false, 'msg' => 'fail to insert media_trip'];

        return ['success' => true];
    }

    public function upload_trip_media_link($trip_id, $media_url, $user_id, $feature_order){
        if($feature_order != 1)return ['success' => false , 'msg' => 'no this order'];
        $media_format = 'url';
        /*--
        * 1.寫入media
        --*/
        $query = Media::create([
            'media_author' => $user_id,
            'media_location_standard' => $media_url,
            'media_format' => $media_format
        ]);
        if(!$query)['success' => false, 'msg' => 'fail to insert url'];
        $media_id = $query->media_id;
        /*--
        * 2.寫入Trip_media
        --*/
        $query = TripMedia::updateOrCreate(
            ['trip_id' => $trip_id, 'feature_order' => $feature_order,'media_type' => 'video_url'],
            ['media_id' => $media_id]
        );
        if(!$query) return ['success' => false, 'msg' => 'fail to insert media_trip'];
        return ['success' => true];
    }

    public function remove_main_trip_media($trip_id, $trip_media_id, $user_id, $feature_order, $media_type){
        if($feature_order != 1) return ['success' => false];
        if(!in_array($media_type,['img','video_url'])) return ['success' => false, 'msg' => 'media type do not exist'];
        $query = Trip::where('trip_author',$user_id)->where('trip_id',$trip_id)->first();
        if(!$query) return ['success' => false, 'msg' => 'have no this trip'];
        $query = TripMedia::where('trips_media_id',$trip_media_id)->where('media_type',$media_type)->where('feature_order',$feature_order)->delete();
        if(!$query) return ['success' => false, 'msg' => 'delete fail'];

        return ['success' => true];
    }
}
?>

