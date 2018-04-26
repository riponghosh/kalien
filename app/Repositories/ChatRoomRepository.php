<?php

namespace App\Repositories;

use App\User;
use App\ChatRoom;
use App\ChatMember;
use App\ChatContent;
use App\Repositories\ErrorLogRepository;
use Illuminate\Support\Facades\DB;

class ChatRoomRepository
{
    protected $chat_room;
    protected $err_log;
    protected $chat_content;
    protected $chat_member;
    protected $user;

    //----------------------
    const CLASS_NAME = 'ChatRoomRepository';
    const ROOM_TYPE_CUSTOMER_SERVICE = 1;   //客服
    protected $customer_service_officer;  //right : 客服人員 id
    const RIGHT_CUSTOMER_SERVICE_OFFICER = 1;   //客服代碼

    public function __construct(User $user, ChatRoom $chat_room, ChatContent $chat_content,ChatMember $chat_member, ErrorLogRepository $errorLogRepository){
        $this->user = $user;
        $this->chat_room = $chat_room;
        $this->chat_content = $chat_content;
        $this->chat_member = $chat_member;
        $this->err_log = $errorLogRepository;
        //Param
        $this->customer_service_officer =  env('CUSTOMER_SERVICE_OFFICER');
    }

    public function create_chat_room($members){
        $chat_members_data = array();

        /*檢查有沒有相同組合member的chat room存在*/
        $member_num = count($members);
        if($this->get_chat_room_id($members) != false) return ['success' => false, 'msg' => 'chatroom is already exist'];
        /*新增chatroom*/
        DB::beginTransaction();
        $create_chat_rooms = $this->chat_room->create();
        $chat_room_id = $create_chat_rooms->id;
        foreach($members as $member){
            array_push($chat_members_data,['chat_room_id' => $chat_room_id, 'members_id' => $member]);
        }
        if(!ChatMember::insert($chat_members_data)) return ['success' => false,'msg' => 'error'];
        DB::commit();

        return ['success' => true,'chat_room_id' => $chat_room_id];


    }

    public function get_chat_room_id($members){
        $member_num = count($members);

        $chat_room_exist = ChatMember::where('chat_room_id',function($q) use($members,$member_num){
            $q->select('chat_room_id')->from('chat_members')->whereIn('members_id',$members)->groupBy('chat_room_id')->havingRaw('COUNT(*) = '.$member_num)->first();
        })->first();

        if(!$chat_room_exist) return false;
        return $chat_room_exist->chat_room_id;
    }

    public function get_chat_room_info_by_id($chat_room_id, $user_id){
        $query = $this->chat_member->where('chat_room_id',$chat_room_id)->get();
        $other_members = [];
        $is_member = false;
        foreach ($query as $k =>$v){
            if($v->members_id == $user_id) {
                $is_member = true;
            }else{
                array_push($other_members, $v->members_id);
            }
        }
        if($is_member == false) return ['success' => false];

        return ['success' => true, 'other_members' => $other_members];

    }

    public function get_chat_content_by_room_id($chat_room_id,$member_id, $rows){
       /*找room*/
        $get_chat_members =  ChatMember::where('chat_room_id',$chat_room_id)->get();
        if(!$get_chat_members) return ['success' => false,'msg' => 'room is not exist'];
        /*檢查是否其中之一member*/
        $is_member = $this->auth_is_member_and_return_all_members($chat_room_id, $member_id);
        if($is_member['success'] == false) return ['success' => false, 'msg' => 'this member does not in room'];
        /*取尾數筆資料*/
        $chat_content = $this->chat_content->where('chat_room_id',$chat_room_id)->orderBy('key','desc')->take($rows)->get();
        if(!$chat_content) return ['success' => false , 'msg' => 'fail to get msgs'];
        return ['success' => true, 'chat_room_id' => $chat_room_id, 'contents' => $chat_content,'count_msg' => count($chat_content)];

    }

    public function create_chat_msg($member_id, $chat_room_id, $content){
        /*檢查是否其中之一member*/
        $is_member = $this->auth_is_member_and_return_all_members($chat_room_id, $member_id);
        if($is_member['success'] == false) return ['success' => false, 'msg' => 'this member does not in room'];
        $other_members = $is_member['other_members'];
        /*取得last key*/
        DB::connection()->getPdo()->exec( 'LOCK TABLES chat_contents WRITE');
        $last_key = $this->chat_content->where('chat_room_id',$chat_room_id)->max('key');
        if(!$last_key){
            $current_key = 1;
        }else{
            $current_key = $last_key + 1;
        }
        /*寫入*/
        $insert_msg = $this->chat_content;
        $insert_msg->chat_room_id = $chat_room_id;
        $insert_msg->content = $content;
        $insert_msg->sent_by = $member_id;
        $insert_msg->key = $current_key;
        $insert_msg->save();

        DB::connection()->getPdo()->exec( 'UNLOCK TABLES' );
        if(!$insert_msg) return ['success' => false, 'msg' => 'save msg fail'];
        return ['success' => true,'key' => $current_key,'other_members' => $other_members];
    }

    public function get_rows_msg_from_key($key, $max_rows, $chat_room_id, $member_id){
        if($max_rows < 2) return ['success' => true];
        /*檢查是否其中之一member*/
        $is_member = $this->auth_is_member_and_return_all_members($chat_room_id, $member_id);
        if($is_member['success'] == false) return ['success' => false, 'msg' => 'this member does not in room'];
        /*取尾數筆資料*/
        $chat_content = $this->chat_content->where('chat_room_id',$chat_room_id)->where('key','<',$key)->orderBy('key','desc')->take($max_rows)->get();
        if(!$chat_content) return ['success' => false , 'msg' => 'fail to get msgs'];

        return ['success' => true,'chat_room_id' => $chat_room_id, 'contents' => $chat_content];
    }
    /*
     * 新增客服房間
     */
    public function create_customer_service_room($members){
        $chat_members_data = array();
        array_push($members, $this->customer_service_officer);
        /*檢查有沒有相同組合member的chat room存在*/
        $member_num = count($members);
        if($this->get_chat_room_id($members) != false) return ['success' => false, 'msg' => 'chatroom is already exist'];
        DB::beginTransaction();
        $create_chat_rooms = $this->chat_room->create(['type' => self::ROOM_TYPE_CUSTOMER_SERVICE]);
        $chat_room_id = $create_chat_rooms->id;
        foreach($members as $member){
            if($member == $this->customer_service_officer) {
                array_push($chat_members_data, [
                    'chat_room_id' => $chat_room_id,
                    'members_id' => $member,
                    'right_of_status' => self::RIGHT_CUSTOMER_SERVICE_OFFICER
                ]);
            }else{
                array_push($chat_members_data, ['chat_room_id' => $chat_room_id, 'members_id' => $member,'right_of_status' => null]);
            }
        }
        if(!ChatMember::insert($chat_members_data)) return ['success' => false,'msg' => 'error'];
        DB::commit();
        $officer_name = User::where('id',$this->customer_service_officer)->first();
        if(!$officer_name) return false;
        $officer_icon_path = isset($officer_name->user_icons[0]) ? $officer_name->user_icons[0]->media->media_location_low : null;
        return [
            'success' => true,
            'service_room_id' => $chat_room_id,
            'service_officer_id' => $this->customer_service_officer,
            'officer_name' => $officer_name->name,
            'officer_icon_path' => $officer_icon_path
        ];
    }

    public function get_customer_service_room_id($members){
        /*
         *  ref_code
         *      1 : room not exist
         *      2 : same user
         *      3 : service user not exist
         */
        if(in_array($this->customer_service_officer, $members)) return ['success' => false, 'msg' => 'same_user', 'ref_code' => 2];
        array_push($members, $this->customer_service_officer);
        $member_num = count($members);
        $chat_room_exist = ChatMember::where('chat_room_id',function($q) use($members,$member_num){
            $q->select('chat_room_id')->from('chat_members')->whereIn('members_id',$members)->groupBy('chat_room_id')->havingRaw('COUNT(*) = '.$member_num)->first();
        })->first();
        if(!$chat_room_exist) return ['success' => false, 'msg' => 'room_not_exist', 'ref_code' => 1];
        /*service officer 資料*/
        $officer_name = User::where('id',$this->customer_service_officer)->first();
        if(!$officer_name) return ['success' => false, 'msg' => 'officer_not_exist', 'ref_code' => 3];
        $officer_icon_path = isset($officer_name->user_icons[0]) ? $officer_name->user_icons[0]->media->media_location_low : null;
        return [
            'success' => true,
            'service_room_id' => $chat_room_exist->chat_room_id,
            'service_officer_id' => $this->customer_service_officer,
            'officer_name' => $officer_name->name,
            'officer_icon_path' => $officer_icon_path
        ];
    }

    /*
    *  (chatlist)chatroom ids : 有content的
    */
    public function get_all_chatroom_ids_with_1_contents_by_user_id($user_id){
        $query = $this->chat_member
                        ->leftJoin('chat_contents', 'chat_contents.chat_room_id', '=', 'chat_members.chat_room_id')
                        ->where('members_id',$user_id)->orderBy('chat_contents.chat_room_id','desc')->get();

        if(!$query){
            $this->err_log->err('get list fail', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }
        $result = array();
        $room_id = null;
        $msg_key = null;
        $msg_content = null;
        $sent_by = null;
        $created_at = null;
        foreach ($query as $k => $v){
            if($room_id == $v->chat_room_id){
                if($v->key >= $msg_key){
                    $msg_key = $v->key;
                    $sent_by = $v->sent_by;
                    $msg_content = $v->content;
                    $created_at = $v->created_at;

                }
            }else{
                if($room_id != null && $msg_key != null){
                    array_push($result,['chat_room_id' => $room_id, 'key' => $msg_key, 'content' => $msg_content, 'sent_by' => $sent_by, 'created_at' => $created_at]);
                }
                $room_id = $v->chat_room_id;
                $msg_key = $v->key;
                $msg_content = $v->content;
                $sent_by = $v->sent_by;
                $created_at = $v->created_at;
            }


        }

        return ['success' => true, 'data' => $result];
    }
    /**
    * 審查
     */
    private function auth_is_member_and_return_all_members($chat_room_id, $member_id){
        $get_members = ChatMember::where('chat_room_id',$chat_room_id)->get();
        $is_member = false;
        $other_members = array();  //存取member
        foreach($get_members as $member){
            if($member_id == $member->members_id){
                $is_member = true;
            }else {
                array_push($other_members, $member->members_id);
            }
        }
        if(!$is_member) return ['success' => false];
        return ['success' => true, 'other_members' => $other_members];
    }

}
?>

