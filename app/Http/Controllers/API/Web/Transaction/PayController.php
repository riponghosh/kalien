<?php
namespace App\Http\Controllers\API\Web\Transaction;

use App\Enums\Pay2GoEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
use App\Repositories\ErrorLogRepository;
use App\Services\Transaction\Pay2GoTwGovService;
use App\Services\Transaction\ReceiptService;
use App\Services\Transaction\TapPayService;
use App\Services\Transaction\TwGovReceiptService;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Auth;
use League\Flysystem\Exception;
use Route;

class PayController extends Controller
{
    protected $tapPayService;
    protected $transactionService;
    protected $userService;
    protected $err_log;
    function __construct(TransactionService $transactionService, TapPayService $tapPayService, ErrorLogRepository $errorLogRepository, UserService $userService)
    {
        $this->tapPayService = $tapPayService;
        $this->transactionService = $transactionService;
        $this->userService = $userService;
        $this->err_log = $errorLogRepository;
        parent::__construct();
    }

    /**
     * @api {post} /api/v1/transaction/pay
     * @apiName PayAction
     * @apiVersion 1.0.0
     * @apiGroup Transaction
     *
     * @apiParam
     * {String} prime_token 智付通用交易token
     *
     *
     *
     */

    function pay(PayRequest $request, ReceiptService $receiptService, Pay2GoTwGovService $pay2GoTwGovService, TwGovReceiptService $twGovReceiptService)
    {
        $data = $request->input();
        //---------------------------------------------------------
        // 資料處理
        //---------------------------------------------------------
        if($data['receipt_carry_type'] != 'null'){
            $data['receipt_donation_code'] = null;
        }else{
            $data['receipt_carry_num'] = null;
        }
        //---------------------------------------------------------
        //  User Data 更新
        //---------------------------------------------------------
        $user_data = array();
        if(empty(Auth::user()->phone_number) && empty( Auth::user()->phone_area_code)){
            $user_data['phone_number'] = $data['phone_number'];
            $user_data['phone_area_code'] = $data['phone_area_code'];
        }
        if(!empty($request['address_for_lottery_mailing']) && empty(Auth::user()->living_address)){
            $user_data['living_address'] = $request['address_for_lottery_mailing'];
        }
        if(count($user_data) > 0){
            $update_user = $this->userService->update_current_user($user_data);
        }
        DB::beginTransaction();
        //---------------------------------------------------------
        //  取得Receipt + total price
        //---------------------------------------------------------
        $receipt = $receiptService->get_by_user_id(Auth::user()->id);
        $receipt_total_price = $receiptService->total_price($receipt);
        $receipt_items = $receiptService->receipt_items($receipt);
        //---------------------------------------------------------
        //  TayPay請款資訊
        //---------------------------------------------------------
        $tap_pay_req_params = [
            'prime' => $request->prime_token,
            'partner_key' => env('TAP_PAY_PARTNER_KEY'),
            'merchant_id' => env('TAP_PAY_MERCHANT_ID'),
            'amount' => $receipt_total_price,
            'details' => 'activity ticket',
            'currency' => CLIENT_CUR_UNIT,
            "cardholder"=> [
                "phone_number"   => '+'.$data['phone_area_code'].$data['phone_number'],
                "name"			=> Auth::user()->name,
                "email"			=> $data['email']
            ],
            "delay_capture_in_days" => 0,
            "instalment"    => 0,
            "remember"      => false,
        ];
        $receipt['total_price'] = $receipt_total_price;
        //----------------------------------------------
        // 移除receipt,購物車; 新增Invoice； 新增票券
        //----------------------------------------------
        $create_ticket = $this->transactionService->transfer_user_receipts_to_invoice_and_create_us_tickets(Auth::user()->id,$tap_pay_req_params);
        if(!$create_ticket['success']) {
            DB::rollback();
            $this->err_log->err('transfer receipt to product fail',__CLASS__,__FUNCTION__);
            $msg = isset($create_ticket['msg']) ? $create_ticket['msg'] : null;
            return ['success' => false, 'msg' => $msg];
        }
        //----------------------------------------------
        // 電子發票-資料預寫入
        //----------------------------------------------
        $create_gov_receipt = $twGovReceiptService->create(
            $create_ticket['invoice_id'],
            $data['invoice_type'],  //0: B2C 2:B2B
            $data['B2B_id'],
            $data['receipt_carry_type'],
            $data['receipt_carry_num'],
            $data['receipt_donation_code'],
            $data['address_for_lottery_mailing']
        );
        //----------------------------------------------
        // 第三方支付
        //----------------------------------------------
        $tap_pay_action_pay = $this->tapPayService->pay($tap_pay_req_params);
        DB::commit();
        //----------------------------------------------
        // 加入付款資訊
        //----------------------------------------------
        $add_tappay_info = $this->transactionService->add_tap_pay_info_in_invoice($create_ticket['invoice_id'], Auth::user()->id, $tap_pay_action_pay);
        if(!$add_tappay_info['success']){
            $this->err_log->err('fail to add tappay info to invoice'.json_encode($tap_pay_action_pay),__CLASS__,__FUNCTION__);
        }
        //----------------------------------------------
        // 電子發票
        //----------------------------------------------
        $invoicing_by_pay2go = $pay2GoTwGovService->invoicing(
            "PN8012".str_pad($create_gov_receipt->invoice_id,6,'0',STR_PAD_LEFT),
            [ // inovice
                'type' => $data['invoice_type'],
                'B2B_id' => $data['B2B_id'],
            ],
            [ // carry
                'type' => $data['receipt_carry_type'],
                'code' => $data['receipt_carry_num'],
            ],
            $data['receipt_donation_code'], //donation_code
            [       // buyer
                'name' => Auth::user()->name,
                'address' =>  $request['address_for_lottery_mailing'],
                'email'  => Auth::user()->email,
                'phone'  => '+'.$data['phone_area_code'].$data['phone_number']
            ],
            $receipt_items
        );
        $twGovReceiptService->add_pay2go_invoicing_response_data($create_gov_receipt->id, $invoicing_by_pay2go);

        //----------------------------------------------
        // Finish
        //----------------------------------------------
        //$this->apiModel->setData($tap_pay_req_params);
        return $this->apiFormatter->success($this->apiModel);
    }
}