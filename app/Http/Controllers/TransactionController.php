<?php
namespace App\Http\Controllers;

use App\QueryFilters\Employee\Merchant\MerchantSearch;
use App\Repositories\ErrorLogRepository;
use App\Services\BankTransferDataBuildService\FubonBankTransferDataBuildService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp\Client;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\DB;

class TransactionController
{
	const CLASS_NAME = __CLASS__;
	protected $userService;
	protected $transactionService;
	protected $err_log;

	public function __construct(TransactionService $transactionService, ErrorLogRepository $errorLogRepository, UserService $userService)
	{
		$this->transactionService = $transactionService;
        $this->userService = $userService;
        $this->err_log = $errorLogRepository;

	}
//-----------------------------------------------------------------
// Cart
//-----------------------------------------------------------------
    public function add_to_cart(Request $request){
        DB::beginTransaction();
        $data['trip_activity_ticket'] = $request->cmds{'product_infos'};
        $action = $this->transactionService->add_to_cart(Auth::user()->id, $data);
        if(!$action['success']){
            DB::rollback();
            return ['success' => false];
        }
        DB::commit();
        return ['success' => true];
    }
    public function del_cart_item(Request $request){
        if(!isset($request->cart_item_id) || $request->cart_item_id == null) return ['success' => false];
        DB::beginTransaction();
        $data['cart_id'] = $request->cart_item_id;
        $action = $this->transactionService->del_cart_item_by_id($data['cart_id'], Auth::user()->id);

        if(!$action['success']){
            DB::rollback();
            return ['success' => false];
        }
        DB::commit();

        return ['success' => true];
    }
//-----------------------------------------------------------------
// Receipt
//-----------------------------------------------------------------
	public function create_reciept_by_cart(Request $request){
        DB::beginTransaction();
		$data = [];
		if(isset($request->cmds{'user_service'})){
            $data['user_service_ticket'] = $request->cmds{'user_service'};
        }
		if(isset($request->cmds{'activity_ticket'})){
            $data['trip_activity_ticket'] = $request->cmds{'activity_ticket'};
        }
        $query = $this->transactionService->create_receipt_from_cart(Auth::user()->id, $data);
        if(!$query['success']){
            DB::rollback();
            return ['success' => false];
        }
        DB::commit();
		return ['success' => true];
	}

	public function create_reciept_for_activity_ticket_direct_purchase(Request $request){
        DB::beginTransaction();
	    $data['trip_activity_ticket'] = $request->cmds{'product_infos'};
        $action = $this->transactionService->create_receipt(Auth::user()->id, $data);
        if(!$action['success']){
            DB::rollback();
            return ['success' => false];
        }
        DB::commit();
        return ['success' => true];
    }
//---------------------------------------------
// Input : user_id, Token : tappay_token
//	Step :  1.取得總價。
//			2.使用第三方支付
//          3.移除購物車和reciepts內容且新增票券
//          4.回傳票券id且建立訂單，訂單明細，應付帳
//
//
//---------------------------------------------

	public function refund_by_us_service_ticket_id(Request $request){
	    //TODO 需要重構
	    return false;
	    /*
        DB::beginTransaction();
		$refund = $this->transactionService->refund_us_service_ticket($request->ticket_id, Auth::user()->id);
		if(!$refund['success']){
            $this->err_log->err('refund fail',self::CLASS_NAME,__FUNCTION__);
            DB::rollback();
            return ['success' => false];
		}
        //----------------------------------------------
        // Tappay退款
        //----------------------------------------------
        $tap_pay_req_params = [
        	'partner_key' => env('TAP_PAY_PARTNER_KEY'),
			'rec_trade_id' => $refund['refund_id'],
			'amount' => $refund['refund_amount']
		];
        $tap_pay_result = null;
        $client = new Client();
        $client->setDefaultOption('headers',['x-api-key' => env('TAP_PAY_PARTNER_KEY')]);
        $res = $client->post(env('TAP_PAY_REFUND_URL'),[],json_encode($tap_pay_req_params));
        try {
            $tap_pay_result = (array)json_decode($res->send()->getBody());
        }catch (\Exception $e)
        {
            DB::rollback();
            $msg = $e;
        }
        if($tap_pay_result['status'] != 0){
            $this->err_log->err('tap pay refund fail'.json_encode($tap_pay_result),self::CLASS_NAME,__FUNCTION__);
            DB::rollback();
            return ['success' => false];
        }

		DB::commit();

        return ['success' => true];
	    */
	}
    //----------------------------------------------
    // 每日結算所有可付款
    // 條件：
    //    acc_payable {is_paid: false, complain: false}
    //    user_service_ticket {end_date: > now + n } n = 3
    //----------------------------------------------
	public function transfer_all_user_account_payables(){
        DB::beginTransaction();
            $result = $this->transactionService->balanced_all_user_ac_payable();
            if(!$result['success']){
                DB::rollback();
            }
        DB::commit();

        return $result;
    }

    public function transfer_all_merchant_account_payable(){
	    DB::beginTransaction();
            $result = $this->transactionService->balanced_all_merchant_ac_payable();
            if(!$result['success']){
                DB::rollback();
            }
	    DB::commit();

        return $result;
    }



}
?>

