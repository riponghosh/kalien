<?php
namespace App\Services;

use App\Repositories\ErrorLogRepository;
use App\Repositories\TripActivityRepository;
use App\Repositories\MediaRepository;
use App\Services\TripActivityTicket\TripActivityTicketService;
use Illuminate\Support\Facades\Auth;

class TripActivityService
{
    protected $tripActivityTicketService;
    protected $tripActivityRepository;
    protected $mediaRepository;
    protected $err_log;

    public function __construct(TripActivityTicketService $tripActivityTicketService, TripActivityRepository $tripActivityRepository, MediaRepository $mediaRepository, ErrorLogRepository $errorLogRepository)
    {
        $this->tripActivityTicketService = $tripActivityTicketService;
        $this->tripActivityRepository = $tripActivityRepository;
        $this->mediaRepository = $mediaRepository;
        $this->err_log = $errorLogRepository;
    }

    public function get_trip_activity($id, $lan){
        $trip_activity = $this->tripActivityRepository->get_trip_activity($id, $lan, 'id');
        if(!$trip_activity) return ['success' => false];
        $trip_activity_ticket = $this->tripActivityRepository->get_trip_activity_ticket_by_activity_id($trip_activity->id, $lan);
        return ['success' => true, 'trip_activity' => $trip_activity, 'trip_activity_ticket' => $trip_activity_ticket];
    }
    public function get_trip_activity_by_uni_name($uni_name, $lan){
        $trip_activity = $this->tripActivityRepository->get_trip_activity($uni_name, $lan, 'uni_name');
        if(!$trip_activity) return ['success' => false];
        $trip_activity_ticket = $this->tripActivityRepository->get_trip_activity_ticket_by_activity_id($trip_activity->id, $lan);
        return ['success' => true, 'trip_activity' => $trip_activity, 'trip_activity_ticket' => $trip_activity_ticket];
    }

    public function get_trip_activities($num, $lan = null){
        if($lan == null){
            $lan = app()->getLocale();
        }
        return $this->tripActivityRepository->get_trip_activities($num, $lan);
    }
    public function update_trip_activity($id, $language, $datas){
        $datas = $this->param_language_convert($datas, $language);
        return $this->tripActivityRepository->update_trip_activity($id, $language, $datas);
    }
    public function update_trip_activity_video_url($id,  $video_url, $user_id){
        return $this->tripActivityRepository->update_trip_activity_video_url($id, $video_url, $user_id);
    }
    public function remove_trip_activity_video_url($id){
        return $this->tripActivityRepository->remove_trip_activity_video_url($id);
    }

    public function create_trip_activity_intro_image($trip_activity_id, $media){
        $name = sha1($trip_activity_id.time().'trip_activity_media');
        $upload_media = $this->mediaRepository->upload_media($media, 'trip_activity/image',$name, Auth::user()->id);

        if($upload_media['success'] == false) return false;

        $insert_activity_gallery = $this->tripActivityRepository->create_trip_image($trip_activity_id, $upload_media['media_id']);
        if(!$insert_activity_gallery) return false;

    }

    public function update_trip_activity_gallery_image($trip_activity_id, $media ){
        $name = sha1($trip_activity_id.time().'trip_gallery_media');
        $upload_media = $this->mediaRepository->upload_media($media, 'trip_activity/image',$name, Auth::user()->id);

        if($upload_media['success'] == false) return false;

        $insert_activity_gallery = $this->tripActivityRepository->update_trip_gallery_image($trip_activity_id, $upload_media['media_id']);
        if(!$insert_activity_gallery) return false;

    }

    public function delete_trip_activity_gallery_image($trip_id, $gallery_media_id){
        $result = $this->tripActivityRepository->delete_trip_gallery_image($trip_id, $gallery_media_id);
        if($result['success'] == false) return ['success' => false ,'msg' => $result['msg']];
        return $result;
    }

    public function update_trip_activity_intro_img_info($trip_id, $trip_img_id, $data, $trip_lan){
        $info_lists = [
            'trip_img_description' => ['attr' => 'description', 'has_lan' => true]
        ];
        $lan_list = ['zh_tw', 'en', 'jp'];
        $input = array();
        //輸入值處理
        foreach ($data as $k => $v){
            if(!isset($info_lists[$k])) return ['success' => false];
            if($info_lists[$k]['has_lan'] == true){
                if(!in_array($trip_lan, $lan_list)) return ['success' => false];
                $attr = $info_lists[$k]['attr'].'_'.$trip_lan;
            }else{
                $attr = $info_lists[$k]['attr'];
            }

            $val = $v;

            $input[$attr] = $val;
        }

        $result = $this->tripActivityRepository->update_trip_activity_media_info($trip_id, $trip_img_id, $input);

        return ['success' => true];
    }
//------------------------------------------------------------
//
//   Activity Ticket
//
//
//------------------------------------------------------------

//------------------------------------------------------------
//  檢查ticket Available(可購買)   //店家是否存在；
//  INPUT : ticket_id, qty,
//------------------------------------------------------------
    public function check_activity_tickets_avalible_for_purchase($activity_tickets = array()){
        if(count($activity_tickets) == 0) return ['success' => false];
        foreach ($activity_tickets as $activity_ticket){
            $start_time = isset($activity_ticket['start_time']) ? $activity_ticket['start_time'] : null;
            //add attrs
            $attr = array();
            if(isset($activity_ticket['is_available_group_for_limit_gp_ticket'])){
                $attr['is_available_group_for_limit_gp_ticket'] = $activity_ticket['is_available_group_for_limit_gp_ticket'];
            }
            $check_tic = $this->tripActivityTicketService->get_allow_purchase_by_id($activity_ticket['ticket_id'], $activity_ticket['start_date'], $start_time, $attr);
            if(!$check_tic){
                $this->err_log->err('ref_code_1:'.json_encode($activity_ticket), __CLASS__, __FUNCTION__);
                $msg = isset($check_tic['msg']) ? $check_tic['msg'] : null;
                return ['success' => false, 'msg' => $msg];
            }
        }
        return ['success' => true];
    }
//------------------------------------------------------------
//
//   Activity Ticket
//
//------------------------------------------------------------
    public function get_activity_ticket_incidental_coupon($ticket_id){
        //檢查有沒有此附屬票存在（不是檢查user有沒有附屬票）
        $get_action = $this->tripActivityRepository->get_activity_ticket_incidental_coupon($ticket_id);
        if(!$get_action) return ['success' => false];

        return ['success' => true, 'data' => $get_action];
    }

    /**
     * 把有語言區分的參數轉換，如 title -> title_zh_tw
     */
    public function param_language_convert($datas, $language){
        if(!in_array($language, ['zh_tw','en','jp'])) return false;
        $ref = ['title','sub_title','map_address','description'];
        foreach ($datas as $data => $value){
            if(in_array($data, $ref)) {
                $datas{$data.'_'.$language} = $value;
                unset($datas{$data});
            }
        }
        return $datas;
    }
}
?>

