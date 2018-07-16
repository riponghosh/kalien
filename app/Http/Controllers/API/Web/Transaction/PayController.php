<?php
namespace App\Http\Controllers\API\Web\Transaction;

use App\EmailAPI;
use App\Enums\Pay2GoEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
use App\Repositories\ErrorLogRepository;
use App\Repositories\UserActivityTicket\UserActivityTicketRepo;
use App\Services\Transaction\Pay2GoTwGovService;
use App\Services\Transaction\ReceiptService;
use App\Services\Transaction\TapPayService;
use App\Services\Transaction\TwGovReceiptService;
use App\Services\TransactionService;
use App\Services\User\CreditAccountService;
use App\Services\UserService;
use App\UserActivityTicket;
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

    function pay(PayRequest $request, ReceiptService $receiptService, Pay2GoTwGovService $pay2GoTwGovService, TwGovReceiptService $twGovReceiptService, CreditAccountService $UserCreditAccountService, EmailAPI $emailAPI)
    {
        $data = $request->input();
        //---------------------------------------------------------
        // 限制使用一種方式
        //---------------------------------------------------------
        $payment_method_count = 0;
        $single_payment_method = '';
        if(isset($data['user_credit_using_amt'])){
            $single_payment_method = 'user_credit';
            $payment_method_count++;
        }
        if(isset($request->prime_token)){
            $single_payment_method = 'credit_card';
            $payment_method_count++;
        }
        if($payment_method_count > 1 || $payment_method_count == 0) throw new Exception('失敗。');
        //---------------------------------------------------------
        // 資料處理
        //---------------------------------------------------------
        if(!empty($data['receipt_carry_type'])){
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

        $receiptService->create_items_payment_attr($receipt);
        $receiptService->items_init_payment_attr_ori_amt_and_final_amt($receipt);
        $receipt_final_total_price = $receiptService->get_final_payment_total_price_by_items_payment_attr();

        $receipt_items = $receiptService->receipt_items($receipt);
        /*---------------------------------------------------------
        //  金額分配：third_payment, 折價券, 帳號餘額等
        //  計算步驟：
        //    原始總價
        //    扣除折價券  TODO 會增加功能use coupon
        //    -------------------
        //    需支付格價
        //    所有支付方式（credit card 、 Pneko credit 等）加權平分
        //
        //
        ---------------------------------------------------------*/
        $total_price_for_payment = $receipt_final_total_price;
        $payment_methods_arr = [];
        //---------------------------------------------------------
        //  使用user credit
        //---------------------------------------------------------
        if($single_payment_method == 'user_credit' && isset($data['user_credit_using_amt']) && $data['user_credit_using_amt'] > 0){
            if(!isset(Auth::user()->credit_account->credit, Auth::user()->credit_account->currency_unit)){
                throw new Exception();
            }
            if($data['user_credit_using_amt'] > cur_convert(Auth::user()->credit_account->credit, Auth::user()->credit_account->currency_unit)){
                throw new Exception();
            }
            if($data['user_credit_using_amt'] < $receipt_final_total_price){
                throw new Exception('帳戶餘額不足。');
            }
            $user_credit_for_payment = $data['user_credit_using_amt'];
            $payment_methods_arr[] = [
                'name' => 'user_credit',
                'amt' => $user_credit_for_payment
            ];
        //---------------------------------------------------------
        //  使用credit card
        //---------------------------------------------------------
        }elseif($single_payment_method == 'credit_card'){
            $credit_card_amt_for_payment = $total_price_for_payment;
            $payment_methods_arr[] = [
                'name' => 'credit_card',
                'amt' => $credit_card_amt_for_payment
            ];
            //---------------------------------------------------------
            //  TayPay請款資訊
            //---------------------------------------------------------
            $tap_pay_req_params = [
                'prime' => $request->prime_token,
                'partner_key' => env('TAP_PAY_PARTNER_KEY'),
                'merchant_id' => env('TAP_PAY_MERCHANT_ID'),
                'amount' => $credit_card_amt_for_payment,
                'details' => 'activity ticket',
                'currency' => 'TWD',
                "cardholder"=> [
                    "phone_number"   => '+'.$data['phone_area_code'].$data['phone_number'],
                    "name"			=>  Auth::user()->name,
                    "email"			=> $data['email']
                ],
                "delay_capture_in_days" => 0,
                "instalment"    => 0,
                "remember"      => false,
            ];
        }else{
            throw new Exception('失敗。');
        }
        $receiptService->allocate_payment_method_for_items_payment_attr($payment_methods_arr);
        //----------------------------------------------
        // 移除receipt,購物車; 新增Invoice； 新增票券
        //----------------------------------------------
        $create_ticket = $this->transactionService->transfer_user_receipts_to_invoice_and_create_us_tickets(
            Auth::user()->id,
            ['amount' => $total_price_for_payment, 'currency' => CLIENT_CUR_UNIT, 'pdt_payment_methods' => $receiptService->items_payment_attr],
            $receipt
        );
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
        // 進行支付
        //----------------------------------------------
        if($single_payment_method == 'credit_card'){
            //----------------------------------------------
            // 第三方支付
            //----------------------------------------------
            $tap_pay_action_pay = $this->tapPayService->pay($tap_pay_req_params);
        }elseif($single_payment_method == 'user_credit'){
            $UserCreditAccountService->decrease_credit($user_credit_for_payment, CLIENT_CUR_UNIT, Auth::user()->id);
        }
        DB::commit();
        //----------------------------------------------
        // 加入付款資訊
        //----------------------------------------------
        if($single_payment_method == 'credit_card'){
            $add_tappay_info = $this->transactionService->add_tap_pay_info_in_invoice($create_ticket['invoice_id'], Auth::user()->id, $tap_pay_action_pay);
            if(!$add_tappay_info['success']){
                $this->err_log->err('fail to add tappay info to invoice'.json_encode($tap_pay_action_pay),__CLASS__,__FUNCTION__);
            }
        }
        //----------------------------------------------
        // 寄送電子票券
        //----------------------------------------------
        try{
            if(!empty($e_tickets = UserActivityTicket::with('owner')->whereIn('ticket_id', $create_ticket['ticket_hash_ids'])->get())){
                foreach ($e_tickets as $e_ticket){
                    $emailAPI->send_e_ticket(
                        $e_ticket->owner->email,
                        $e_ticket->sub_title,
                        $e_ticket->name,
                        $e_ticket->owner->name,
                        $e_ticket->start_date,
                        optional(optional($e_ticket['relate_gp_activity'])['user_group_activity'])['start_time'],
                        optional(optional($e_ticket->Trip_activity_ticket)->Trip_activity)->map_address_zh_tw,
                        env('APP_URL').'/activity_ticket/use/'.$e_ticket->ticket_id
                    );
                }

            };
        }catch (Exception $e){

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