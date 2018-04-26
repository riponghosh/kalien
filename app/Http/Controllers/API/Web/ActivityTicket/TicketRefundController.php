<?php
namespace App\Http\Controllers\API\Web\ActivityTicket;

use App\Http\Controllers\Controller;
use App\Services\UserActivityTicket\ActivityTicketService;
use Auth;
use DB;
use League\Flysystem\Exception;

class TicketRefundController extends Controller
{
    protected $transactionService;
    protected $tapPayService;
    protected $userGroupActivityService;
    protected $activityTicketService;
    protected $pay2GoTwGovService;
    protected $twGovReceiptService;

    function __construct(ActivityTicketService $activityTicketService)
    {
        $this->activityTicketService = $activityTicketService;
        parent::__construct();
    }

    function activity_ticket_refund(){
        $request = request()->input();
        $tic_owner = Auth::user()->id;
        DB::beginTransaction();
        $this->activityTicketService->refund_by_owner($request['ticket_id'], $tic_owner);
        DB::commit();
        return $this->apiFormatter->success($this->apiModel);
    }
}