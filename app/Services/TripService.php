<?php
namespace App\Services;
use App\Repositories\TripRepository;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripService
{
    public $TripRepository;
    public $MediaRepository;
    public function __construct(TripRepository $TripRepository, MediaRepository $mediaRepository)
    {
        $this->TripRepository = $TripRepository;
        $this->MediaRepository = $mediaRepository;
    }

    public function get_current_user_all_trip_introduction($user_id){
        return $this->TripRepository->get_current_all_trip($user_id);
    }
    public function get_user_all_trip_introduction($user_id, $allow_published = false){
        return $this->TripRepository->get_all_trip($user_id, $allow_published);
    }
    public function create_trip($user_id, $input){
        $pubish = isset($input->publish) ? $input->publish : 'published';
        return $this->TripRepository->add_trip($user_id,$pubish);
    }

    public function del_trip($trip_id, $user_id){
      return $this->TripRepository->del_trip($trip_id, $user_id);
    }
    public function update_trip_status($trip_id, $status){
        return $this->TripRepository->update_trip_status($trip_id, $status);
    }
    public function update_trip($trip_id, $input, $user_id){
        $output = array();
        if(isset($input->topic)){ $output['trip_title'] = $input->topic;};
        if(isset($input->description)){ $output['trip_description'] = $input->description;};
        if(isset($input->map_url)){ $output['map_url'] = $input->map_url;};
        if(isset($input->map_address)){ $output['map_address'] = $input->map_address;};
        if(isset($input->external_link)){ $output['external_link'] = $input->external_link;};
        $result = $this->TripRepository->update_trip($trip_id, $user_id, $output);
        return $result;
    }
    public function upload_trip_media($trip_id, $media, $user_id, $order){
        DB::beginTransaction();
        $media_token = sha1($user_id + strtotime("now"));
        $media_url = $this->MediaRepository->upload_media($media, 'trip/orginal', $media_token, $user_id);
        if(!$media_url['success']) return false;
        $result = $this->TripRepository->upload_trip_media($trip_id, $media_url['media_id'], $user_id, $order);
        DB::commit();
        return $result;
    }
    public function upload_trip_main_media_link($trip_id, $media_url, $user_id, $feature_order){
        return $this->TripRepository->upload_trip_media_link($trip_id, $media_url, $user_id, $feature_order);
    }
    public function remove_main_trip_media($trip_id, $trip_media_id, $user_id, $feature_order, $media_type){
        return $this->TripRepository->remove_main_trip_media($trip_id, $trip_media_id, $user_id, $feature_order, $media_type);
    }
}
?>

