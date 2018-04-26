<?php
namespace App\Http\Controllers\API\Web\GroupActivity;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Services\UserGroupActivityService;
use Auth;
use DB;
use League\Flysystem\Exception;

class GroupActivityController extends Controller
{
    protected $transactionService;
    protected $userGroupActivityService;
    function __construct(TransactionService $transactionService ,UserGroupActivityService $userGroupActivityService)
    {
        $this->userGroupActivityService = $userGroupActivityService;
        $this->transactionService = $transactionService;
        parent::__construct();
    }

    function apply_for_join_in(){
        $request = request()->input();
        //-------------------------------------------------------
        //  status 2: 已是其中一位參加者，但是否要繼續購票
        //-------------------------------------------------------
        //-------------------------------------------------------
        //  數據
        //-------------------------------------------------------
        /*購票數量*/
        $ticket_qty =  !isset($request['ticket_qty']) || empty($request['ticket_qty']) ? 1 : $request['ticket_qty'];
        if($ticket_qty > 100){  //TODO 增加手續費時才解除限制
           throw new Exception('不能大量購買。');
        }
        DB::beginTransaction();
        //-------------------------------------------------------
        //  已是其中一位參加者且強制入團（可代人購票再轉讓）
        //-------------------------------------------------------
        $known_is_participant = !empty($request['known_is_participant']) ? $request['known_is_participant'] : false;
        /*TODO Direct buy 判斷*/
        $get_activity_info = $this->userGroupActivityService->get_by_gp_activity_id($request['gp_activity_id'], true, Auth::user()->id, $known_is_participant);
        //---------------------------------
        // 檢查最大可購買票數
        //---------------------------------
        if($get_activity_info['limit_joiner'] != null){
            if($get_activity_info['limit_joiner'] < ($ticket_qty + count($get_activity_info['applicants'])  )){
                throw new Exception('購買數量已超出可售額。');
            }
        }
        //---------------------------------
        // 新增 Receipt
        //---------------------------------
        $receipt_data = array();
        $receipt_data['trip_activity_ticket'] = array();
        $trip_activity_data = [
            'start_date' => $get_activity_info['start_date'],
            'start_time' => $get_activity_info['start_time'],
            'ticket_id' => $get_activity_info['activity_ticket_id'],
            'transfer_incidental_coupon_to_user_id' => $get_activity_info['host_id'],
            'relate_gp_activity_id' => $request['gp_activity_id'],
            'qty' => $ticket_qty
        ];
        if($get_activity_info['is_available_group_for_limit_gp_ticket']){
            $trip_activity_data['is_available_group_for_limit_gp_ticket'] = true;
        }
        array_push($receipt_data['trip_activity_ticket'],$trip_activity_data);
        $create_receipt = $this->transactionService->create_receipt(Auth::user()->id, $receipt_data);
        if(!$create_receipt['success']){
            $msg = isset($create_receipt['msg']) ? $create_receipt['msg'] : '加入失敗';
            throw new Exception($msg);
        }
        DB::commit();
        return $this->apiFormatter->success($this->apiModel);
    }
}