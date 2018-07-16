<?php

namespace App\Http\Controllers\API\Web\Employee\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant\Merchant;
use App\QueryFilters\Employee\Merchant\MerchantSearch;
use App\Services\BankTransferDataBuildService\FubonBankTransferDataBuildService;
use App\Services\MerchantService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use League\Flysystem\Exception;

class CreditAccountController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function get_bank_transfer_data(Request $request, FubonBankTransferDataBuildService $fubonBankTransferDataBuildService, MerchantSearch $merchantSearch){
        /*
        $data = [
            ['bank_code' => '013','account_number' => '1231231234' ,'amt' => 23,'acc_name' => 'Derrick','desc' => 'PnekoSettlement', 'email' => 'derrickho723@gmail.com', 'mail_content' => 'PnekoSettlement'],
            ['bank_code' => '005','account_number' => '4252312343' ,'amt' => 23.01, 'acc_name' => 'VitaBank','desc' => 'PnekoSettlement', 'email' => 'derrickho723@gmail.com', 'mail_content' => 'PnekoSettlement'],
            ['bank_code' => '123','account_number' => '4252312343' ,'amt' => 243.0123,'acc_name' => 'Vita Bank','desc' => 'PnekoSettlement', 'email' => 'derrickho723@gmail.com', 'mail_content' => 'PnekoSettlement'],
        ];
        */
        $request->request->add(['with_credit_accounts' => true]);
        $request->request->add(['has_tw_bank_account']);
        $data = $merchantSearch->apply($request);

        $total_amt = 0.00;

        $data_collection = collect($data);

        $data_collection = $data_collection->filter(function ($val){
           if(empty($val->merchant_credit_account)) return false;
           $bank_code = $val->merchant_credit_account->bank_code;
           $acc_number = $val->merchant_credit_account->bank_account;
           $cur_unit = $val->merchant_credit_account->currency_unit;

           return (!empty($bank_code)) && (!empty($acc_number)) && (!empty($cur_unit));
        });
        $data = $data_collection->map(function($val){
            $amt = optional($val->merchant_credit_account)->credit;
            $amt_tw = cur_convert($amt,$val->merchant_credit_account->currency_unit,'TWD');
            $amt_opt = round(floor($amt_tw),2);

            $d = [
              'bank_code' => optional($val->merchant_credit_account)->bank_code,
              'account_number' => optional($val->merchant_credit_account)->bank_account,
              'amt' => $amt_opt,
              'email' => $val->email,
              'mail_content' => 'PnekoSettlement',
              'acc_name' => '',
              'desc' => 'PnekoSettlement'
            ];
            return $d;
        })->toArray();

        $total_amt += $data_collection->sum(function ($val){
            $amt = optional($val->merchant_credit_account)->credit;
            $amt_tw = cur_convert($amt,$val->merchant_credit_account->currency_unit,'TWD');
            return $amt_opt = round(floor($amt_tw),2);
        });

        $data_str = $fubonBankTransferDataBuildService->bank_data_converter($data, $err_report);
        $bank_header_str = $fubonBankTransferDataBuildService->create_transfer_header(
            Carbon::now('Asia/Taipei')->addHours(1)->format('Y/m/d'),
            $data_collection->count(),
            $total_amt
        );
        $err_report = array();
        $this->apiModel->setData([
            'bank_accounts' => $data,
            'bank_header_str' => $bank_header_str,
            'bank_data_str' =>$data_str,
            'err' => $err_report,
            'total_amt' => $total_amt

        ]);
        return $this->apiFormatter->success($this->apiModel);
    }

    function withdraws_multi_account(Request $request, Merchant $merchant, MerchantService $merchantService){
        //output result
        $not_searchable_merchants = [];
        $transfer_success_result = [];
        $transfer_err_result = [];

        $data = $request->toArray();
        $account_data = $data['account_data'];
        $account_data_collection = collect($account_data);
        $account_data_key_by_email = $account_data_collection->keyBy('email');
        $merchant_emails = array_pluck($account_data, 'email');
        $merchants = $merchant->with('merchant_credit_account')->whereIn('email', $merchant_emails)->whereHas('merchant_credit_account')->get();
        $not_searchable_merchants = array_diff($merchant_emails, array_pluck($merchants, 'email'));
        DB::beginTransaction();
        $merchants_collection = collect($merchants);
        $merchants_collection->each(function($val) use ($account_data_key_by_email, $merchantService,&$transfer_success_result, &$transfer_err_result){
            $amt = $account_data_key_by_email[$val->email]['amt'];
            $amt_unit = $account_data_key_by_email[$val->email]['amt_unit'];
            try{
                $transfer_success_result[] = $merchantService->account_withdrawal($val->id,$amt, $amt_unit, '手動轉帳');
            }catch (\Exception $e){
                $transfer_err_result[] = ['email' => $val->email, 'reason' => $e->getMessage()];
            }

        });
        DB::commit();
        $this->apiModel->setData(
            [
                'ori_data' => $data,
                'merchant' => $merchants,
                'not_searchable_merchants' => $not_searchable_merchants,
                'transfer_success_result' => $transfer_success_result,
                'transfer_err_result' => $transfer_err_result
            ]
        );
        return $this->apiFormatter->success($this->apiModel);
    }

}