<?php
namespace App\Http\Controllers\API\Web\Employee;
use App\Http\Controllers\Controller;
use App\Services\MerchantService;
use Carbon\Carbon;
use App\Services\UserGroupActivityService;

class MerchantController extends Controller
{
    protected $merchantService;
    function __construct(MerchantService $merchantService)
    {
        parent::__construct();
        $this->merchantService = $merchantService;
    }

    function get_gp_activities_by_trip_activity_ticket_id(UserGroupActivityService $userGroupActivityService){
        $data = request()->input();
        $result = $userGroupActivityService->get_by_activity_ticket_id($data['activity_ticket_id'], [
            'limit_activities' => 20,
            'is_not_expired' => true,
            'is_available_group_for_limit_gp_ticket' => 0,
            'query_start_date' => Carbon::now()->toDateTimeString(),
            'query_end_date' => Carbon::now()->addDay(45)->toDateTimeString()
        ]);
        $this->apiModel->setData($result);
        return $this->apiFormatter->success($this->apiModel);
    }

    function merchant_account_withdrawal(){
        $data = request()->input();
        $result = $this->merchantService->account_withdrawal($data['merchant_id'], $data['amount'], $data['amount_unit']);
        return $this->apiFormatter->success($this->apiModel);
    }
}