<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use App\User;
use App\Repositories\UserProfileRepository;
use App\Repositories\ErrorLogRepository;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\PushMessage;

use App\Services\ChatRoomService;
use Illuminate\Support\Facades\Storage;

class ChatroomController extends Controller
{

	protected $chatRoomService;
	protected $userService;
	protected $userProfileRepository;
    protected $err_log;

    const CLASS_NAME = 'ChatroomController';

	public function __construct(ChatRoomService $chatRoomService, UserProfileRepository $userProfileRepository,UserService $userService, ErrorLogRepository $errorLogRepository)
	{
		$this->chatRoomService = $chatRoomService;
		$this->userProfileRepository = $userProfileRepository;
		$this->userService = $userService;
		$this->err_log = $errorLogRepository;
	}

	public function get_chat_room_by_id($chat_room_id){
		$user_id = Auth::user()->id;
		$chat_room = $this->chatRoomService->get_chat_room_info_by_id($chat_room_id, $user_id);
		if(!$chat_room['success']) return ['success' => false];
		$member_infos = [];
		//other_user
		foreach ($chat_room['other_members'] as $member_id){
            $user = $this->userProfileRepository->get_user($member_id);
            if($user){
                $user_icon = $this->userService->get_current_use_icon_by_User($user); //放入user array
				if($user_icon != null) $user_icon = Storage::url($user_icon->media->media_location_low);
				$user_data = ['name' => $user->name, 'icon_path' => $user_icon, 'id' => $user->id];
            	array_push($member_infos, $user_data);
			}else{
				$this->err_log->err('get member data fail',self::CLASS_NAME, __FUNCTION__);
			}
		}
		//current user
        $user_icon = $this->userService->get_current_use_icon_by_User(Auth::user()); //放入user array
        if($user_icon != null) $user_icon = $user_icon->media->media_location_low;
		$c_user_data = ['name' => Auth::user()->name, 'icon_path' => $user_icon, 'id' => Auth::user()->id];

		return ['success' => true, 'other_users' => $member_infos, 'current_user' => $c_user_data];
	}

	public function sendMsg(Request $request){
        if($request->content == null || $request->content == '') return ['success' => false];
        $result = $this->chatRoomService->send_msg(Auth::user()->id, $request->room_id, $request->content);
        if($result['success'] == false) return ['success' => false];

        return array('key' => $result['key'],'success' => true);
	}

	public function get_msg($room_id){
        $result = $this->chatRoomService->get_chat_room_with_last_rows_msg($room_id, Auth::user()->id, 15);
        if($result['success'] == false) return ['success' => false];
        return ['success' => true,'data' => $result['contents']];
		//return $this->chatRoomService->get_msg(Auth::user()->id,$room_id,20,null);
	}
	public function get_msg_one($room_id){
		return $this->chatRoomService->get_msg(Auth::user()->id,$room_id,1,null);
	}
	public function get_more_msg($room_id,$last_msg_key_from_client){
        $result = $this->chatRoomService->get_more_msg($last_msg_key_from_client, 20, $room_id, Auth::user()->id);
        if($result['success'] == false) return ['success' => false];
        return ['success' => true,'data' => $result['contents']];
		//return $this->chatRoomService->get_msg($room_id,20,$last_msg_key_from_client);
	}

	/*chat list*/
	public function get_room_list_with_last_content(){
		$query = $this->chatRoomService->get_chat_room_ids_with_no_null_content(Auth::user()->id);
		if($query['success'] == false) return ['success' => false];
		return ['success'=> true, 'data' => $query['data']];

	}


}
?>
