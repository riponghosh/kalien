<?php
namespace App\Http\Controllers\API\Web\Employee;

use App\AccountPayableContract;
use App\Http\Controllers\Controller;
use App\QueryFilters\Employee\AcPayableContract\AcPayableContractSearch;
use App\Repositories\AcPayableContract\AcPayableContractRepo;
use App\Repositories\Merchant\MerchantRepo;
use App\Services\AcPayableContract\AcPayableContractService;
use App\Services\MerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionContractController extends Controller
{
    protected $acPayableContractService;
    function __construct( AcPayableContractService $acPayableContractService)
    {
        parent::__construct();
        $this->acPayableContractService = $acPayableContractService;
    }

    function show_payable_contract(Request $request, AcPayableContractService $acPayableContractService, AcPayableContractSearch $acPayableContractSearch){

        $contracts = $acPayableContractSearch->apply($request);

        $this->apiModel->setData($contracts);
        return $this->apiFormatter->success($this->apiModel);
    }

    function settlement(Request $request, AcPayableContractRepo $acPayableContractRepo, MerchantService $merchantService){
        DB::beginTransaction();
        $data = [
            'search_results_amt' => 0,
            'contracts' => [],
            'not_use_pdt_contracts' => [],
            'records' => [],
            'merchant_account_operations' => []
        ];
        $filters = [];
        if($request->has('balancable_from')){
            $filters['balancable_from'] = $request->balancable_from;
        }
        if($request->has('balancable_to')){
            $filters['balancable_to'] = $request->balancable_to;
        }
        if($request->has('merchant_ids')){
            $filters['merchant_ids'] = $request->merchant_ids;
        }
        //取出所有可結算合約
        if( empty($contracts = $acPayableContractRepo->get_allow_settled_contracts($filters)) ){
            $this->apiModel->setData($data);
            return $this->apiFormatter->success($this->apiModel);
        };
        $data['contracts'] = array_pluck($contracts, 'id');

        //產品已使用的合約 only
        if(!count($contracts = $this->acPayableContractService->get_only_the_product_is_used($contracts))){
            $this->apiModel->setData($data);
            return $this->apiFormatter->success($this->apiModel);
        };
        $data['not_use_pdt_contracts'] = array_diff($data['contracts'],array_pluck($contracts, 'id'));

        //contracts update to balanced
        if(!$update_to_balanced = AccountPayableContract::whereIn('id', array_pluck($contracts, 'id'))->update(['is_paid' => true, 'balanced_at' => date('y-m-d H:i:s')])){
            DB::rollback();
            $this->apiModel->setMsg('failed to balanced');
            $this->apiModel->setData($data);
            return $this->apiFormatter->error($this->apiModel);
        }

        //create collection
        $contracts_collection = collect($contracts);

        $contracts_collection = $this->acPayableContractService->get_settlement_fee($contracts_collection);
        //Write Payable Contracts Record
        if(!$records = $this->acPayableContractService->create_records($contracts)){
            DB::rollback();
            $this->apiModel->setMsg('failed to create record');
            $this->apiModel->setData($data);
            return $this->apiFormatter->error($this->apiModel);
        };
        $data['records'] = $records;

        //amt transfer to merchant account
        $settled_contract_for_merchants = $contracts_collection->filter(function($v){
            return !empty($v['merchant_id']);
        });
        foreach ($settled_contract_for_merchants as $settled_contract_for_merchant){
            $data['merchant_account_operations'][] = $merchantService->increase_credit_account_by_balance_payable_contract( $settled_contract_for_merchant['merchant_id'], $settled_contract_for_merchant['merchant_revenue'], $settled_contract_for_merchant['currency_unit'], 'PN_'.$settled_contract_for_merchant['id']);
        }

        DB::commit();

        $this->apiModel->setData($data);
        return $this->apiFormatter->success($this->apiModel);
    }
}
