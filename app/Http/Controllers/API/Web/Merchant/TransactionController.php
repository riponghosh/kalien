<?php

namespace App\Http\Controllers\API\Web\Merchant;

use App\Formatter\Merchant\TransactionRecordFormatter;
use App\Http\Controllers\Controller;
use App\Services\AcPayableContract\AcPayableContractService;
use Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    protected $acPayableContractService;
    function __construct(AcPayableContractService $acPayableContractService)
    {
        $this->acPayableContractService = $acPayableContractService;
        parent::__construct();
    }

    function get_sales_record(TransactionRecordFormatter $formatter){
        $request = request()->input();
        $merchant_member = Auth::user()->id;

        $query_filter = [
            'merchant_id' => $merchant_member,
            'is_balanced' => true
        ];

        if(isset($request['start_date'])){
            $query_filter['query_settlement_start_date'] =  Carbon::createFromFormat('Y-m-d', $request['start_date'])->toDateString();
        }else{
            $query_filter['query_settlement_start_date'] = Carbon::now()->toDateTimeString();
        }

        if(isset($request['end_date'])){
            $query_filter['query_settlement_end_date'] =  Carbon::createFromFormat('Y-m-d', $request['end_date'])->toDateString();
        }


        $records = $this->acPayableContractService->get($query_filter);

        $data = $formatter->dataFormat($records);

        $this->apiModel->setData($data);
        return $this->apiFormatter->success($this->apiModel);
    }
}