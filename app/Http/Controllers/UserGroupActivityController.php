<?php
namespace App\Http\Controllers;

use App\Formatter\TripActivity\TripActivityFormatter;
use App\Repositories\ErrorLogRepository;
use App\Services\TransactionService;
use App\Services\TripActivityService;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use App\Services\UserGroupActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class UserGroupActivityController extends Controller
{
    protected $userGroupActivityService;
    protected $tripActivityService;
    protected $transactionService;
    protected $userService;
    protected $err_log;

    function __construct(TripActivityService $tripActivityService, UserGroupActivityService $userGroupActivityService, TransactionService $transactionService, UserService $userService, ErrorLogRepository $errorLogRepository)
    {
        $this->tripActivityService = $tripActivityService;
        $this->userGroupActivityService = $userGroupActivityService;
        $this->transactionService = $transactionService;
        $this->userService = $userService;
        $this->err_log = $errorLogRepository;
    }


    function show_group_activity(Request $request, TripActivityFormatter $tripActivityFormatter){
        try{
            $get_group_activity = $this->userGroupActivityService->get_by_gp_activity_id($request->gp_activity_id,false,null,false,true);
        }catch (Exception $e){
            return abort(404);
        }
        $group_activity = $get_group_activity;
        //TODO Query 了trip acitivity兩次，需優化
        $get_trip_activity = $this->tripActivityService->get_trip_activity($group_activity['trip_activity']['id'],app()->getLocale());
        if(!$get_trip_activity['success']){
            return abort(404);
        }else{
            $trip_activity = $tripActivityFormatter->dataFormat($get_trip_activity['trip_activity']);
        }
        $organiser =  $this->userService->get_user($request->host_id);

        return view('groupActivity.groupActivity', compact('group_activity', 'organiser','trip_activity'));

    }
//---------------------------------------------------------------
// API
//----------------------------------------------------------------
    function create_gp_activity(Request $request){
        $validator = Validator::make($request->all(),[
            'activity_ticket_id' => 'required',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'duration' => 'numeric',

        ]);
        if($validator->fails()){
            return ['success' => false,'msg' => '資料格式有誤。','status' => $validator->errors()->all()];
        }
        $action = $this->userGroupActivityService->create_group_activity(
            Auth::user()->id,
            $request->activity_ticket_id,
            $request->activity_title,
            $request->start_date,
            $request->start_time,
            $request->duration,
            'min',
            $request->limit_joiner
        );

        return ['success' =>true, 'gp_activity_url' => '/group_events/'.$action['gp_activity_id']];
    }


}
?>