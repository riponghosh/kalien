<?php
namespace App\Http\Controllers\API\Web\Merchant;

use App\Http\Controllers\Controller;
use App\Services\TripActivityTicket\TripActivityTicketService;
use App\Services\UserActivityTicket\ActivityTicketService;
use App\Services\MerchantService;
use App\Services\TransactionService;
use App\Services\UserGroupActivityService;
use Auth;
use DB;
use Carbon\Carbon;
use League\Flysystem\Exception;

class GroupActivityController extends Controller
{
    protected $groupActivityService;
    protected $merchantService;
    protected $tripActivityTicketService;
    protected $activityTicketService;
    function __construct(UserGroupActivityService $userGroupActivityService ,MerchantService $merchantService, TripActivityTicketService $tripActivityTicketService, ActivityTicketService $activityTicketService)
    {
        $this->tripActivityTicketService = $tripActivityTicketService;
        $this->activityTicketService = $activityTicketService;
        $this->groupActivityService = $userGroupActivityService;
        $this->merchantService = $merchantService;
        parent::__construct();
    }
    /**
     * @api {post} /api-merchant/v1/group_activity/get
     * @apiName GetGpActivityByMerchant
     * @apiVersion 1.0.0
     * @apiGroup GroupActivity

     * @apiParam
     *  {String} activity_ticket_id
     *  {String} start_date
     *
     * @apiSuccess
     *  {Int} id
     *  {Int} host_id
     *  {String} gp_activity_id
     *  {Int} limit_joiner
     *  {Date} start_date
     *  {Time} start_time
     *  {Boolean} allow_cancel_gp_by_merchant
     *  {Array} applicants
     *
     *
     * @apiSuccessExample {json} Success-Response:
        {
            "id": 65,
            "host_id": 1,
            "activity_title": "",
            "activity_ticket_id": 1,
            "start_time": "11:00:00",
            "start_date": "2018-03-30",
            "duration": 60,
            "duration_unit": "min",
            "gp_activity_id": "1000118030265",
            "limit_joiner": 2,
            "deleted_at": null,
            "created_at": "2018-03-02 09:54:23",
            "updated_at": "2018-03-02 09:54:23",
            "timezone": "Asia\/Taipei",
            "is_achieved": 0,
            "invalid_by_merchant": 0,
     *      "allow_cancel_gp_by_merchant": 1,
            "applicants": []
        }
     *
     */
    function get_by_trip_activity_ticket_id(UserGroupActivityService $userGroupActivityService){
        $request = request()->input();
        $merchant_id = Auth::user()->id;

        $query_filter = [
            'limit_activities' => 20,
            'is_not_expired' => true,
            'is_achieved' => 1,
        ];
        //判斷有否指定票劵
        if(!empty($request['activity_ticket_ids'])){
            $activity_tickets = $this->tripActivityTicketService->get_by_ids($request['activity_ticket_ids']);

            foreach ($activity_tickets as $activity_ticket){
                if($activity_ticket->Trip_activity->merchant_id != $merchant_id){
                    throw new Exception('沒有此產品訪問權。');
                }
            }
        }else{
            $activity_tickets = $this->tripActivityTicketService->get_all_by_trip_activity([
                'merchant_id' => $merchant_id
            ]);
        }

        if(isset($request['start_date'])){
            $query_filter['query_start_date'] =  Carbon::createFromFormat('Y-m-d', $request['start_date'])->toDateString();
        }else{
            $query_filter['query_start_date'] = Carbon::now()->toDateTimeString();
        }

        if(isset($request['end_date'])){
            $query_filter['query_end_date'] =  Carbon::createFromFormat('Y-m-d', $request['end_date'])->toDateString();
        }

        $gp_activities = $userGroupActivityService->get_by_activity_ticket_id(array_pluck($activity_tickets,'id'), $query_filter,['allow_cancel_by_merchant']);
        $this->apiModel->setData($gp_activities);
        return $this->apiFormatter->success($this->apiModel);
    }

    function cancel_gp_activity(UserGroupActivityService $userGroupActivityService){
        $request = request()->input();
        $merchant_id = Auth::user()->id;
        //------------------------------------------------------------
        // 檢查商家有否資格
        //------------------------------------------------------------
        $gp_activity = $userGroupActivityService->get_by_gp_activity_id($request['gp_activity_id']);
        if($gp_activity['trip_activity']['merchant_id'] != $merchant_id){
            throw new Exception('沒有操作權限。');
        }
        //------------------------------------------------------------
        // 取消團
        //------------------------------------------------------------
        $this->groupActivityService->invalid_by_merchant($gp_activity['id']);
        foreach ($gp_activity->user_activity_tickets as $ticket){
            $this->activityTicketService->refund_by_ticket_id($ticket->ticket_id);
        }
        $this->groupActivityService->delete($gp_activity['id']);

        return $this->apiFormatter->success($this->apiModel);

    }
}