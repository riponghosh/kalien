<?php
namespace App\Services;

use App\Repositories\ChatRoomRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use Illuminate\Http\Request;

use App\Events\PushMessage;

class ChatRoomService
{
  /*models container*/
    protected $chatRoomRepository;

    public $msg = array(
              'key'     => '',
              'content'  => '',
              'sent_by'  => '',             
              'created_at'=> ''
              
          );

    public function __construct(ChatRoomRepository $chatRoomRepository)
    {
      $this->chatRoomRepository = $chatRoomRepository;
    }

    /*************************************************************
     **Mysql
     *************************************************************/
    public function get_id_or_create_chat_room($members){
        $get_id = $this->chatRoomRepository->get_chat_room_id($members);
        if($get_id != false) return $get_id;
        $create_chat_room = $this->chatRoomRepository->create_chat_room($members);
        if($create_chat_room['success'] == false) return false;
        return $create_chat_room['chat_room_id'];
    }

    public function create_chat_room($members){
        return $this->chatRoomRepository->create_chat_room($members);

    }

    public function get_chat_room_info_by_id($chat_room_id, $user_id){
        return $this->chatRoomRepository->get_chat_room_info_by_id($chat_room_id, $user_id);

    }

    public function get_chat_room_id($members){
        return $this->chatRoomRepository->get_chat_room_id($members);
    }
    public function send_msg($member_id, $chat_room_id, $content){
        $result = $this->chatRoomRepository->create_chat_msg($member_id, $chat_room_id, $content);
        if($result['success'] == false) return ['success' => false];
        $msg = array('key' => $result['key'],'content' => $content, 'sent_by' => $member_id);
        /*send to other room user*/
        foreach($result['other_members'] as $member_id){
            event(new PushMessage($member_id,$msg,$chat_room_id));
        };

        return $result;
    }
    public function get_chat_room_with_last_rows_msg($chat_room_id, $member_id, $rows){
        return $this->chatRoomRepository->get_chat_content_by_room_id($chat_room_id, $member_id, $rows);
    }
    public function get_chat_room_with_last_rows_mgs_by_members_id($members, $rows){
        /*房間不存在create,return room_id,content*/
        $room_id = $this->get_id_or_create_chat_room($members);
        if(!$room_id) return false;
        $chat_room = $this->get_chat_room_with_last_rows_msg($room_id, Auth::user()->id, $rows);
        if(!$chat_room)return false;
        return ['room_id' => $room_id, 'count_msg' => $chat_room['count_msg'],'content' => $chat_room['contents']];
    }
    public function get_more_msg($msg_key, $max_rows, $chat_room_id, $member_id){
        return $this->chatRoomRepository->get_rows_msg_from_key($msg_key, $max_rows, $chat_room_id, $member_id);
    }
    /*----------------
    *     Service room
    ----------------*/
    public function get_customer_service_room_id($members, $create_if_null=false){
        //如果same user return null;
        $get_id = $this->chatRoomRepository->get_customer_service_room_id($members);
        if($get_id['success'] == false){
            if($get_id['ref_code'] == 2 || $get_id['ref_code'] == 3) return false;
            if($get_id['ref_code'] == 1){
                if($create_if_null == true) {
                    $create_chat_room = $this->chatRoomRepository->create_customer_service_room($members);
                    if ($create_chat_room['success'] == false) return false;
                    return $create_chat_room;
                }
            }
        }elseif($get_id['success'] == true){
            return $get_id;
        }

        return false;
    }

    public function create_customer_service_room(){
        if(Auth::guest()) return false;
        return $this->chatRoomRepository->create_customer_service_room([Auth::user()->id]);
    }

    public function get_chat_room_ids_with_no_null_content($user_id){
        return $this->chatRoomRepository->get_all_chatroom_ids_with_1_contents_by_user_id($user_id);
    }

}
?>

