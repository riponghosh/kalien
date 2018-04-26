<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Services\ActivityTicketService;
use App\Services\MerchantService;
use App\Services\TransactionService;
use App\Services\UserGroupActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use App\Services\TripActivityService;
use App\Services\TripActivity\TripActivityService as TripActivityServiceV2;
use App\Repositories\UserProfileRepository;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    protected $activityTicketService;
    protected $transactionService;
    protected $UserProfileRepository;
    protected $userGroupActivityService;
    protected $userService;
    protected $tripActivityService;
    protected $merchantService;

    public function __construct(ActivityTicketService $activityTicketService, TransactionService $transactionService, UserGroupActivityService $userGroupActivityService, UserProfileRepository $UserProfileRepository, UserService $userService, TripActivityService $tripActivityService, MerchantService $merchantService){
        $this->activityTicketService = $activityTicketService;
        $this->transactionService = $transactionService;
        $this->userGroupActivityService = $userGroupActivityService;
        $this->UserProfileRepository = $UserProfileRepository;
        $this->userService = $userService;
        $this->merchantService = $merchantService;
        $this->tripActivityService = $tripActivityService;
    }

    public function show_dashboard(){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        //data
        $merchants = $this->merchantService->get_all_merchants_by_employee();

        return view('/employeeManagement/dashboard',compact('user','user_icon', 'merchants'));
    }
    public function show_trip_activity_gallery(){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        $trip_activity_cards = $this->tripActivityService->get_trip_activities(7);

        return view('/employeeManagement/trip_activity_gallery',compact('user','user_icon','trip_activity_cards'));
    }

    public function show_trip_activity_ticket(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        //data
        $trip_activity = $this->tripActivityService->get_trip_activity_by_uni_name($request->uni_name, 'zh_tw');

        return view('/employeeManagement/tripActivityTicket',compact('user','user_icon','trip_activity'));
    }

    public function create_trip_activity(Request $request, TripActivityServiceV2 $tripActivityServiceV2){

        $new_trip_activity = $tripActivityServiceV2->create();
        if(empty($new_trip_activity->id)) return abort(500);
        $lan = in_array($request->cookie('web_language'), ['zh_tw','jp','en']) ? $request->cookie('web_language') : 'zh_tw';
        return redirect()->action('EmployeeController@show_trip_activity',['id' => $new_trip_activity->id,'lan' => $lan]);

    }

    public function show_trip_activity(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        $trip_activity_id = $request->id;
        $get_trip_activity = $this->tripActivityService->get_trip_activity($trip_activity_id, $request->lan);
        if(!$get_trip_activity['success']) return abort(404);
        $trip_activity = $get_trip_activity['trip_activity'];
        //$trip_activity_ticket = $get_trip_activity['trip_activity_ticket'];
        $trip_language = $request->lan;

        return view('/employeeManagement/trip_activity_editor',compact('user','user_icon','trip_activity_id','trip_activity','trip_language'));
    }

    public function update_trip_activity(Request $request){
        $input = Input::only('title','sub_title','map_address','map_url','description');
        $update = $this->tripActivityService->update_trip_activity($request->id, $request->trip_language, $input);
        return redirect()->back();


    }

    public function update_trip_activity_video_url(Request $request){
        $input = Input::only('video_url');
        if($request->video_url == null || $request->video_url == ''){
            $remove = $this->tripActivityService->remove_trip_activity_video_url($request->id);
        }else{
            $update = $this->tripActivityService->update_trip_activity_video_url($request->id, $input['video_url'], Auth::user()->id);
        }
        return redirect()->back();

    }
    public function update_trip_activity_gallery_image(Request $request){
        if(!$request->hasFile('trip_main_gallery_img'))return redirect()->back();

        $validator = Validator::make($request->all(),[
            'trip_main_gallery_img' => 'mimes:jpeg,bmp,png,gif,jpg|max:10240',
        ]);
        if($validator->fails()){
            return redirect()->back();
        }
        $this->tripActivityService->update_trip_activity_gallery_image($request->id, $request->file('trip_main_gallery_img'));
        return redirect()->back();

    }

    public function create_trip_activity_intro_img(Request $request){
        DB::beginTransaction();

        if(!$request->hasFile('trip_activity_intro_img'))return redirect()->back();
        $validator = Validator::make($request->all(),[
            'trip_activity_intro_img' => 'mimes:jpeg,bmp,png,gif,jpg|max:10240',
        ]);
        if($validator->fails()){
            return redirect()->back();
        }

        $create = $this->tripActivityService->create_trip_activity_intro_image($request->trip_activity_id, $request->file('trip_activity_intro_img'));
        if(!$create){
            DB::rollback();
            return redirect()->back();
        }
        DB::commit();
        return redirect()->back();
    }

    public function update_trip_activity_intro_img_info(Request $request){
        DB::beginTransaction();
            if(!isset($request->trip_activity_id)){
                return ['success' => false, 'id' => $request->trip_activity_id];
            }
            $input = Input::only('trip_img_description');
            $update = $this->tripActivityService->update_trip_activity_intro_img_info($request->trip_activity_id, $request->trip_img_id, $input, $request->trip_language);
            if(!$update['success']){
                return ['success' => false];
            }

        DB::commit();

        return ['success' => true];
    }
    public function delete_trip_activity_gallery_image(Request $request){
        $result = $this->tripActivityService->delete_trip_activity_gallery_image($request->trip_activity_id, $request->media_id);
        if($result['success'] == false ) return $result;
        return ['success' => true];
    }

    protected function basic_user_data(){
        $user = $this->UserProfileRepository->get_current_user();
        $user_icon = $this->userService->get_current_use_icon_by_User($user);

        return ['user' => $user, 'user_icon' => $user_icon];
    }
//------------------------------------------------------------------------------------------------
//  Merchant
//------------------------------------------------------------------------------------------------
    public function check_merchant_available(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        $get_merchant = $this->merchantService->get_merchant_info($request->merchant_uni_name);
        if(!$get_merchant['success']){
            $merchant = false;
        }else{
            $merchant = $get_merchant['data'];
        }


        return view('/employeeManagement/merchant_info',compact('user','user_icon', 'merchant'));
    }

    public function show_merchant_transaction_record_page(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];
        $get_merchant = $this->merchantService->get_merchant_info($request->merchant_uni_name);
        if(!$get_merchant['success']){
            $merchant = false;
        }else{
            $merchant = $get_merchant['data'];
        }

        $get_merchant_record = $this->merchantService->get_merchant_payable_contracts($get_merchant['data']['id'],
            [
                'settlement_start_date' =>'2017-10-01',
                'settlement_end_date'=>'2017-12-31'
            ]);
        if(!$get_merchant_record['success']){
            $merchant_records = null;
        }else{
            $merchant_records = $get_merchant_record['data'];
        }
        return view('/employeeManagement/merchantInfo/merchantTransactionRecord',compact('user','user_icon','merchant','merchant_records'));
    }
//------------------------------------------------------------------------------------------------
//  用戶餘額操作
//------------------------------------------------------------------------------------------------
    //---------------------
    //  幫用戶充𠊙
    //---------------------
    public function charge_user_credit(Request $request){
        DB::beginTransaction();
        $action = $this->transactionService->charge_user_credit_by_employee(
            Auth::user()->id,
            $request->ac_owner_id,
            $request->charge_amount,
            $request->charge_amount_unit,
            $request->desc
        );
        if(!$action['success']){
            $msg = isset($action['msg']) ? $action['msg'] : '失敗';
            DB::rollback();
            return ['success' => false, 'msg' => $msg];
        }
        DB::commit();

        return $action;
    }
//------------------------------------------------------------------------------------------------
//  交易合約
//------------------------------------------------------------------------------------------------
    public function show_transaction_record(Request $request){
        $user_data = $this->basic_user_data();
        $user = $user_data['user'];$user_icon = $user_data['user_icon'];

        return view('/employeeManagement/transaction_record',compact('user','user_icon'));
    }
    public function get_ac_payable_contract_record(Request $request){
        $duration_start = $request->duration_start;
        $duration_end = $request->duration_end;
        $role = $request->role;
        $role_id = $request->role_id;
        if(!in_array($role, ['1','2','3'])){
            return ['success' => false, 'msg' => '角色錯誤'];
        }
        $action = $this->transactionService->get_ac_payable_contract_record($role, $role_id, $duration_start,$duration_end);
        return $action;
    }
}


?>

