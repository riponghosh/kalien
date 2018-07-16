<?php
namespace App\Http\Controllers;

use App\Formatter\Homepage\GroupActivityFormatter;
use App\Repositories\UserGroupActivity\UserGroupActivityRepo;
use App\Services\ChatRoomService;
use App\Services\UserGroupActivityService;
use League\Flysystem\Exception;
use Request;
use Auth;
use Illuminate\Support\Facades\File;


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
        ]);
        if(!Auth::guest()){
            $service_room = $chatRoomService->get_customer_service_room_id([Auth::user()->id],true);
            if(!$service_room)  $service_room = null;
        }

        return view('home',compact('gallery_activity','searchResult','service_room','group_activities'));
    }

    function index_mobile_v2(UserGroupActivityRepo $groupActivityRepo, ChatRoomService $chatRoomService, GroupActivityFormatter $groupActivityFormatter){
        $gp_activity_rows = json_decode(File::get("../storage/envData/homePageGroupActivityData.json"), true);
        $group_activity_ids = array_flatten(array_pluck($gp_activity_rows, 'ids'));
        $get_group_activities = $groupActivityRepo->whereCond([['id', '', $group_activity_ids, 'In']])->get();

        //export data
        $group_activities = array();
        if(!empty($get_group_activities)){
            $collection = collect($get_group_activities);
            foreach ($gp_activity_rows as $j => $row){
                $export_row = array(
                    'title' => $row['title'],
                    'gp_cards' => array()
                );
                foreach ($row['ids'] as $gp_id){
                    if($gp_card = $collection->firstWhere('id', $gp_id)){
                        $export_row['gp_cards'][] = $groupActivityFormatter->dataFormat($gp_card);
                        // dd($groupActivityFormatter->dataFormat($gp_card));
                    };
                }
                $group_activities[] = $export_row;
            }
        }
        /*客服*/
        $service_room = null;
        if(!Auth::guest()){
            $service_room = $chatRoomService->get_customer_service_room_id([Auth::user()->id],true);
            if(!$service_room)  $service_room = null;
        }

        return view('mobiles.homePage',compact('group_activities'));
    }
}
?>