<?php
namespace App\Http\Controllers\API\Web\Employee;
use App\Http\Controllers\Controller;
use App\QueryFilters\Employee\Merchant\MerchantSearch;
use App\Services\AcPayableContract\AcPayableContractService;
use App\Services\MerchantService;
use App\Services\TripActivity\TripActivityService;
use Carbon\Carbon;
use App\Services\UserGroupActivityService;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    protected $merchantService;
    function __construct(MerchantService $merchantService)
    {
        parent::__construct();
        $this->merchantService = $merchantService;
    }

    function get(Request $request){
        $result = MerchantSearch::apply($request);

        $data = $result;
        $this->apiModel->setData($data);
        return $this->apiFormatter->success($this->apiModel);
    }

    function show($id){
        $data = request()->input();
        $merchant = $this->merchantService->first($id);

        $this->apiModel->setData($merchant);
        return $this->apiFormatter->success($this->apiModel);
    }

    function show_products($id, TripActivityService $tripActivityService){
        $pdts = $tripActivityService->get(['merchant_id' => $id]);

        $this->apiModel->setData($pdts);
        return $this->apiFormatter->success($this->apiModel);
    }

    function show_payable_contract($id, AcPayableContractService $acPayableContractService){
        $data = request()->input();
        $attr = $data;
        $conditions = [];
        if(isset($data['settlement_start_date'])){
            $conditions['settlement_start_date'] = $data['settlement_start_date'];
        }
        if(isset($attr['settlement_end_date'])){
            $conditions['settlement_end_date'] = $data['settlement_end_date'];
        }
        $conditions['merchant_id'] = $id;
        $contracts = $acPayableContractService->get($conditions);
        $this->apiModel->setData($contracts);
        return $this->apiFormatter->success($this->apiModel);
    }

    function get_gp_activities_by_trip_activity_ticket_id(UserGroupActivityService $userGroupActivityService){
        $data = request()->input();
        $result = $userGroupActivityService->get_by_activity_ticket_id($data['activity_ticket_id'], [
            'limit_activities' => 20,
            'is_not_expired' => true,
            'is_achieved' => 0,
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