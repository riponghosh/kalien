<?php
namespace App\Http\Controllers;

use App\Services\ActivityTicketService;
use App\Services\TripActivityTicket\TripActivityTicketService;
use Illuminate\Http\Request;
use App\Services\MerchantService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class MerchantController extends Controller
{
    protected $activityTicketService;
    protected $tripActivityTicketService;
    protected $merchantService;
    protected $userService;
    function __construct(ActivityTicketService $activityTicketService, MerchantService $merchantService, TripActivityTicketService $tripActivityTicketService, UserService $userService)
    {
        $this->activityTicketService = $activityTicketService;
        $this->merchantService = $merchantService;
        $this->tripActivityTicketService = $tripActivityTicketService;
        $this->userService = $userService;
    }

    function show_activity_code_page(){
        return view('merchant.activityCodePage');
    }

    function create_merchant_member_by_act_code(Request $request){
        DB::beginTransaction();
        if(!isset($request->merchant_act_code)) return abort(404);
        $action = $this->merchantService->create_merchant_member_by_act_code(Auth::user()->id, $request->merchant_act_code);
        if(!$action['success']) {
            DB::rollback();
            $err_msg = isset($action['msg']) ? $action['msg'] : '無效的驗證碼。';
            return back()->withErrors(['error' => [$err_msg]]);
        }
        DB::commit();

        return redirect('/merchant');

    }

    function show_merchant_dashboard(){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        //data
        $action = $this->merchantService->get_all_merchants_by_user_id(Auth::user()->id);
        if(!$action['success']){
            $merchants = null;
        }else{
            $merchants = $action['data'];
        }

        return view('merchant.dashboard',compact('user','user_icon', 'merchants'));
    }

    function show_activity_ticket(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        //data
        try{
            $trip_activity_tickets = $this->tripActivityTicketService->get_all_by_trip_activity_uni_name($request->uni_name);
        }catch (Exception $e){
            return abort(404);
        }

        $merchants = $this->merchantService->get_all_by_member(Auth::user()->id);
        $merchant_ids = array_pluck($merchants, 'id');
        foreach ($trip_activity_tickets as $trip_activity_ticket){
            if(!in_array($trip_activity_ticket->merchant_id, $merchant_ids, true)){
                return abort(404);
            };
        }

        $user_activity_tickets = $this->activityTicketService->get_all_user_activity_tickets_by_activity_uni_name_for_merchant($request->uni_name, Auth::user()->id);
        $user_activity_tickets = $user_activity_tickets['success'] ? $user_activity_tickets['data'] : null;

        return view('/merchant/tripActivityTicket',compact('user','user_icon','user_activity_tickets', 'trip_activity_tickets'));
    }
    function show_merchant_info(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        //data
        $action = $this->merchantService->get_merchant_by_id($request->merchant_uni_name, Auth::user()->id);
        if(!$action['success']) return abort(404);
        $merchant = $action['data'];
        return view('merchant/merchant_index',compact('user','user_icon','merchant'));
    }

    protected function basic_user_data(){
        $user = Auth::user();
        $user_icon = $this->userService->get_current_use_icon_by_User($user);

        return ['user' => $user, 'user_icon' => $user_icon];
    }
}

?>