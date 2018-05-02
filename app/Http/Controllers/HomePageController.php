<?php
namespace App\Http\Controllers;

use App\Services\ChatRoomService;
use App\Services\UserGroupActivityService;
use League\Flysystem\Exception;
use Request;
use Auth;

class HomePageController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index(Request $request, ChatRoomService $chatRoomService, UserGroupActivityService $userGroupActivityService){
        $searchResult = null;
        /*Gallery 活動*/
        try{
            $gallery_activity = $userGroupActivityService->get_by_gp_activity_id(env('HomePageGalleryActivityId'));
        }catch (Exception $e){

        }

        /*客服*/
        $service_room = null;
        /*group activity*/
        /*group activity*/
        $group_activities = $userGroupActivityService->get_group_activities([
            'limit_activities' => 9,
            'is_not_expired' => true,
            'is_available_group_for_limit_gp_ticket' => 0
        ]);
        if(!Auth::guest()){
            $service_room = $chatRoomService->get_customer_service_room_id([Auth::user()->id],true);
            if(!$service_room)  $service_room = null;
        }

        return view('home',compact('gallery_activity','searchResult','service_room','group_activities'));
    }
}
?>