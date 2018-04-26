<?php
namespace App\Services;

use App\Enums\Pay2GoEnum;
use App\Enums\ProductTicketTypeEnum;
use App\Exceptions\Transaction\CreateTwGovReceiptFail;
use App\Exceptions\Transaction\TapPay\TapPayPayFail;
use App\Repositories\GovInvoiceRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserCreditAcOperationRecordRepository;
use App\Repositories\UserTicketRepository;
use App\Services\Transaction\ReceiptService;
use App\Services\TripActivityTicket\TripActivityTicketService;
use App\Services\UserGroupActivityService;
use App\Repositories\TransactionRepository;
use App\Repositories\UserCreditAccountRepository;
use App\Services\GuideTicketService;
use App\Repositories\ErrorLogRepository;
use App\TapPayResponse;
use App\UserCreditAcOperationRecord;
use GuzzleHttp\Client;
use League\Flysystem\Exception;

class TransactionService
{
	const CLASS_NAME = __CLASS__;
    const TIC_TYPE_US_SERVICE = 1;
    const TIC_TYPE_ACTIVITY = 2;

	protected $guideTicketService;
	protected $receiptService;
	protected $tripActivityTicketService;
	protected $tripActivityService;
	protected $activityTicketService;
	protected $govInvoiceRepository;
	protected $userGroupActivityService;
	protected $userCreditAcOperationRecordRepository;
	protected $err_log;
	protected $tapPayResponse;
	protected $transactionRepository;
	protected $merchantRepository;
	protected $userCreditAccountRepository;
	protected $userTicketRepository;

	public function __construct(TripActivityTicketService $tripActivityTicketService, ReceiptService $receiptService, TapPayResponse $tapPayResponse, GovInvoiceRepository $govInvoiceRepository,GuideTicketService $guideTicketService, ActivityTicketService $activityTicketService, UserCreditAcOperationRecordRepository $userCreditAcOperationRecordRepository, UserTicketRepository $userTicketRepository,UserGroupActivityService $userGroupActivityService, MerchantRepository $merchantRepository,TripActivityService $tripActivityService, ErrorLogRepository $errorLogRepository, TransactionRepository $transactionRepository, UserCreditAccountRepository $userCreditAccountRepository)
	{
	    $this->tripActivityTicketService = $tripActivityTicketService;
	    $this->tapPayResponse = $tapPayResponse;
	    $this->govInvoiceRepository = $govInvoiceRepository;
		$this->guideTicketService = $guideTicketService;
		$this->receiptService = $receiptService;
		$this->tripActivityService = $tripActivityService;
		$this->userGroupActivityService = $userGroupActivityService;
		$this->activityTicketService = $activityTicketService;
		$this->err_log = $errorLogRepository;
		$this->userCreditAcOperationRecordRepository = $userCreditAcOperationRecordRepository;
		$this->transactionRepository = $transactionRepository;
		$this->merchantRepository = $merchantRepository;
		$this->userCreditAccountRepository = $userCreditAccountRepository;
		$this->userTicketRepository = $userTicketRepository;
	}

	//------------------------------------------
	// INPUT :  user_service, activity_ticket
	//  每一次都會清空,不分種類
	//	$data :  [
	//
	// 				user_service_ticket: {
	//						{qty, product_id}  註：type = user_service : qty = 1
	//				}
	//              activity_ticket: {
	//						{qty, product_id}
	//				}
	// 			 ]
	//------------------------------------------

//------------------------------------------
//  Cart
//------------------------------------------
    public function add_to_cart($user_id, $data){
	    $err_result = ['success' => false];
        if(isset($data['trip_activity_ticket']) && count($data['trip_activity_ticket']) > 0){
            foreach ($data['trip_activity_ticket'] as $d){
                if(!isset($d['start_date']) || strtotime($d['start_date']) < strtotime("now")){
                    $err_result['msg'] = 'Use date invalid';
                    $this->err_log->err('ref_code: 1', self::CLASS_NAME, __FUNCTION__);
                    return $err_result;
                };
                if(!isset($d['ticket_id'])){
                    $this->err_log->err('ref_code: 2', self::CLASS_NAME, __FUNCTION__);
                    return $err_result;
                }
                if(!isset($d['qty']) || $d['qty'] < 1){
                    $err_result['msg'] = 'qty invalid';
                    $this->err_log->err('ref_code: 3', self::CLASS_NAME, __FUNCTION__);
                    return $err_result;
                }
            }
            //----------------------------------
            //  Check activity_ticket available
            //----------------------------------
            $product_available = $this->tripActivityService->check_activity_tickets_avalible_for_purchase($data['trip_activity_ticket']);
            if(!$product_available['success']){
                $this->err_log->err('ref_code: 4', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }

            $query = $this->transactionRepository->create_cart_items($user_id, $data['trip_activity_ticket']);
            if(!$query) return ['success' => false];

        }
        return ['success' => true];
    }

    public function get_all_cart_items($user_id){
        $query = $this->transactionRepository->get_all_cart_items_by_user_id($user_id);
        if(!$query){
            $this->err_log->err('ref_code: 1', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }
        return ['success' => true, 'data' => $query];
    }

    public function del_cart_item_by_id($cart_id, $user_id){
        $query = $this->transactionRepository->del_cart_by_id($cart_id, $user_id);
        if(!$query) return ['success' => false];
        return ['success' => true];
    }
//------------------------------------------
//  Receipt
//------------------------------------------
    public function create_receipt($user_id, $data){
        //------------------------------------------
        //  Clear Receipt
        //------------------------------------------
        $clear_user_receipt = $this->receiptService->delete_by_user_id($user_id);
        if(!$clear_user_receipt){
            $this->err_log->err('clear receipt fail', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }
        //------------------------------------------
        // user service
        //------------------------------------------
        if(isset($data['user_service_ticket']) && count($data['user_service_ticket']) > 0){
            $create_us_service_products = $this->add_products_user_services_to_receipt($user_id, $data['user_service_ticket']);
            if(!$create_us_service_products['success']){
                $this->err_log->err('ref_code: 1', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }
        }
        //------------------------------------------
        // Activity ticket
        //------------------------------------------
        if(isset($data['trip_activity_ticket']) && count($data['trip_activity_ticket']) > 0){
            $create_trip_activity_ticket = $this->add_product_user_activity_tickets_to_receipt($user_id, $data['trip_activity_ticket']);
            if(!$create_trip_activity_ticket['success']){
                $this->err_log->err('ref_code: 2', self::CLASS_NAME, __FUNCTION__);
                $msg = isset($create_trip_activity_ticket['msg']) ? $create_trip_activity_ticket['msg'] : null;
                return ['success' => false, 'msg' => $msg];
            }
        }
        return ['success' => true];
    }

	public function create_receipt_from_cart($user_id, $data){
			//------------------------------------------
			//  Clear Receipt
			//------------------------------------------
			$clear_user_receipt = $this->receiptService->delete_by_user_id($user_id);
			if(!$clear_user_receipt){
				$this->err_log->err('clear receipt fail', self::CLASS_NAME, __FUNCTION__);
				return ['success' => false];
			}
			//------------------------------------------
			// user service
			//------------------------------------------
			if(isset($data['user_service_ticket']) && count($data['user_service_ticket']) > 0){
				$create_us_service_products = $this->add_products_user_services_to_receipt($user_id, $data['user_service_ticket']);
				if(!$create_us_service_products['success']){
                    $this->err_log->err('ref_code: 1', self::CLASS_NAME, __FUNCTION__);
					return ['success' => false];
				}
			}
            //------------------------------------------
            // Activity ticket
            //------------------------------------------
            if(isset($data['trip_activity_ticket']) && count($data['trip_activity_ticket']) > 0){
                $create_trip_activity_ticket = $this->add_cart_items_to_receipt($user_id, $data['trip_activity_ticket']);
                if(!$create_trip_activity_ticket['success']){
                    $this->err_log->err('ref_code: 2', self::CLASS_NAME, __FUNCTION__);
                    return ['success' => false];
                }
            }
			return ['success' => true];


	}

	public function get_reciept_by_user_id($user_id){
		$query_get_reciept = $this->transactionRepository->get_receipt_by_user_id($user_id);
		if(!$query_get_reciept['success']) return ['success' => false];
		$user_services_tickets = (object)$query_get_reciept['user_services_tickets']->groupBy('guide_service_ticket.request_to_id');
		$trip_activity_tickets = (object)$query_get_reciept['trip_activity_tickets'];
		return ['success' => true, 'user_service_tickets' => $user_services_tickets, 'trip_activity_tickets' => $trip_activity_tickets];


	}
//------------------------------------------------------------------------------------
//  data: [{prdorequest_id}]
//------------------------------------------------------------------------------------
    public function add_cart_items_to_receipt($user_id, $data){
	    $cart_ids = array_pluck($data, 'cart_id');
	    $get_cart_items = $this->transactionRepository->get_cart_items_by_id($user_id, $cart_ids);
        if(!$get_cart_items){
            return ['success' => false];
        }
        //-----------------------------------------
        //  檢查
        //-----------------------------------------
        $check_cart_ids = array_pluck($get_cart_items ,'id');
        foreach($cart_ids as $cart_id){
            if(!in_array($cart_id, $check_cart_ids)){
                $this->err_log->err('ref_code: 1', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }
        }
        //-----------------------------------------
        //  寫入
        //-----------------------------------------
        $all_receipt_data = array();
        foreach ($get_cart_items as $cart_item){
            $receipt_data['cart_id'] = $cart_item['id'];
            $receipt_data['ticket_id'] = $cart_item['product_id'];
            $receipt_data['qty'] = $cart_item['qty'];
            $receipt_data['start_date'] = $cart_item['start_date'];
            array_push($all_receipt_data, $receipt_data);
        }
        $action = $this->add_product_user_activity_tickets_to_receipt($user_id, $all_receipt_data, true);
        if(!$action['success']){
            $this->err_log->err('ref_code: 2', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }
        return ['success' => true];
    }

	public function add_products_user_services_to_receipt($user_id, $data){
		$product_available = $this->guideTicketService->check_guide_service_requests_available(array_pluck($data, 'product_id'), $user_id);
		if(!$product_available['success']){
			$this->err_log->err('info: '.json_encode($product_available), self::CLASS_NAME, __FUNCTION__);

			return ['success' => false];
		}
		//----------------------------
		//  data {product_id => , qty => }
		//----------------------------
		$create = $this->transactionRepository->create_user_services_receipt($user_id, $data);
		if(!$create['success']){
			$this->err_log->err('fail to insert to sql', self::CLASS_NAME, __FUNCTION__);
			return ['success' => false];
		}

		return ['success' => true];
	}
	public function add_product_user_activity_tickets_to_receipt($user_id, $data = array(), $transfer_from_cart = false){
	    $err_result = ['success' => false, 'msg' => ''];
        //----------
        //  Valid data:{date(rq), time, tiket_id(rq), qty(rq)}
        //----------
        if(count($data) == 0){
            $this->err_log->err('ref_code: 1', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false, 'msg' => 'no ticket info'];
        }
        foreach ($data as $d){
            if(!isset($d['ticket_id'])){
                return $err_result;
            }
            if(!isset($d['qty']) || $d['qty'] < 1){
                $this->err_log->err('ref_code: 3', self::CLASS_NAME, __FUNCTION__);
                $err_result['msg'] = 'qty invalid';
                return $err_result;
            }
        }
        //----------------------------------
        //  Check activity_ticket available
        //----------------------------------
        $product_available = $this->tripActivityService->check_activity_tickets_avalible_for_purchase($data);
        if(!$product_available['success']) {
            $msg = isset($product_available['msg']) ? $product_available['msg'] : null;
            $this->err_log->err('ref_code: 4', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false, 'msg' => $msg];
        }
        //----------------------------------
        // Add Tickets to Receipt
        //----------------------------------
        $create = $this->transactionRepository->create_activity_tickets_receipt($user_id, $data, $transfer_from_cart);
        if(!$create['success']) {
            $this->err_log->err('ref_code: 5', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        };
        return ['success' => true];

    }
//------------------------------------------------------------------------------------
//  轉移成可用票券
//  交易流程配對：     新增產品 ; 應收帳款=第三方支付成功(Invoice) ； ac_payable_contract(Seller)
//------------------------------------------------------------------------------------
    public function transfer_user_receipts_to_invoice_and_create_us_tickets($user_id, $third_party_request){
        //------------------------------------------------
        //  取出receipt
        //------------------------------------------------
        $get_receipts = $this->receiptService->get_by_user_id($user_id)->groupBy('product_type');
        //------------------------------------------------
        //  檢查票券是否能購買
        //------------------------------------------------
        if($get_receipts[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET] != null){
            foreach ($get_receipts[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET] as $trip_activity_ticket){
                $attr = array();
                if(isset($trip_activity_ticket['gp_activity']['is_available_group_for_limit_gp_ticket']) && $trip_activity_ticket['gp_activity']['is_available_group_for_limit_gp_ticket'] == true){
                    $attr['is_available_group_for_limit_gp_ticket'] = true;
                }
                $check = $this->tripActivityTicketService->get_allow_purchase_by_id($trip_activity_ticket['product_id'], $trip_activity_ticket['start_date'], $trip_activity_ticket['start_time'], $attr);
                if(!$check){
                    $msg = isset($check['msg']) ? $check['msg'] : null;
                    return ['success' => false, 'msg' => $msg];
                }
            }
        }
        //------------------------------------------------
        // 移除receipt,cart->新增invoice->新增user_ticket
        //------------------------------------------------
        $transfer = $this->transactionRepository->delete_cart_and_receipts_by_rts_id_and_create_invoice_and_user_tickets_and_ac_payable_contract($user_id, $get_receipts, $third_party_request);
        if(!$transfer['success']){
            $this->err_log->err('delete_cart_and_receipts_by_rts_id_and_create_invoice_and_user_tickets_and_ac_payable_contract fail',__CLASS__,__FUNCTION__);
            return ['success' => false];
        }
        if(!$this->receiptService->delete_by_user_id($user_id)){
            $this->err_log->err('delete_receipt_failed',__CLASS__,__FUNCTION__);
          return ['success' => false];
        };
        //------------------------------------------------
        // 團體活動加入 跟票券轉移
        //------------------------------------------------
        if(isset($transfer['gp_activity_and_joiners']) && count($transfer['gp_activity_and_joiners']) > 0){
            foreach ($transfer['gp_activity_and_joiners'] as $gp_activity_and_joiner){
                if(isset($gp_activity_and_joiner['apply_to_is_available_group_for_limit_gp_ticket'])){
                    $apply_to_is_available_group_for_limit_gp_ticket = $gp_activity_and_joiner['apply_to_is_available_group_for_limit_gp_ticket'];
                }else{
                    $apply_to_is_available_group_for_limit_gp_ticket = false;
                }
                // TODO  限制判斷事件觸發
                $action = $this->userGroupActivityService->add_participant($gp_activity_and_joiner['gp_activity_id'],$user_id, $apply_to_is_available_group_for_limit_gp_ticket);
                if(!$action){
                    $this->err_log->err('join gp fail gp_activity_id: '.$gp_activity_and_joiner['gp_activity_id'],__CLASS__, __FUNCTION__);
                    return ['success' => false, 'msg' => isset($action['msg']) ? $action['msg'] : null];
                };
            }
        }
        //------------------------------------------------
        // Finish
        //------------------------------------------------

        return ['success' => true, 'invoice_id' => $transfer['invoice_id']];
    }
//------------------------------------------------------------------------------------
//  交易成功後，把tappay資料寫入inovice
//------------------------------------------------------------------------------------
    public function add_tap_pay_info_in_invoice($invoice_id, $user_id, $third_party_res){
        $this->tapPayResponse->create([
            'invoice_id' => $invoice_id,
            'user_id' => $user_id,
            'tappay_record' => json_encode($third_party_res),
            'is_passed' => 0
        ]);
        if(!$this->transactionRepository->update_invoice_tap_pay_info($invoice_id, $user_id, $third_party_res)) return ['success' => false];
        $this->tapPayResponse->update(['is_passed' => 1]);
        return ['success' => true];
    }

//------------------------------------------------------------------------------------
//  結算
//------------------------------------------------------------------------------------
    public function balanced_all_user_ac_payable(){
        $msg = '';
        $acc_payable_balanced = $this->transactionRepository->update_user_ac_payable_contracts_to_balanced_at();
        if(!$acc_payable_balanced['success']){
            return ['success' => false, 'msg' => $msg = $msg.$acc_payable_balanced['msg']];
        }
        if(count($acc_payable_balanced['data']) > 0){
            foreach ($acc_payable_balanced['data'] as $transfer){
                $transfer_process = $this->userCreditAccountRepository->update_user_credit($transfer['final_transfer_amount'], $transfer['final_transfer_amount_unit'], 'increase', $transfer['seller_id']);
                if(!$transfer_process['success']){
                    return ['success' => false];
                }
            }
        }

        return ['success' => true, 'msg' => $msg = $msg.$acc_payable_balanced['msg']];

    }

    public function balanced_all_merchant_ac_payable(){
        $msg = '';
        $acc_payable_balanced = $this->transactionRepository->update_merchant_ac_payable_contracts_to_balanced_at();
        if(!$acc_payable_balanced['success']){
            return ['success' => false, 'msg' => $msg = $msg.$acc_payable_balanced['msg']];
        }
        if(count($acc_payable_balanced['data']) > 0){
            foreach ($acc_payable_balanced['data'] as $transfer){
                $transfer_process = $this->merchantRepository->update_merchant_credit($transfer['final_transfer_amount'], $transfer['final_transfer_amount_unit'], 'increase', $transfer['merchant_id'], 'balance_ac_payable_contract','PN_'.$transfer['id']);
                if(!$transfer_process){
                    return ['success' => false];
                }
            }
        }

        return ['success' => true];

    }
//--------------------------------------------------------------------
//  用戶Pneko幣
//--------------------------------------------------------------------
    public function charge_user_credit_by_employee($employee_id, $user_id, $charge_amount, $amount_unit,$desc = ''){
        $record_id = $this->userCreditAcOperationRecordRepository->create_before_operated_record($user_id, $charge_amount, $amount_unit, $desc, 1);

        if($charge_amount <= 0){
            $this->userCreditAcOperationRecordRepository->change_record_status_to_failed($record_id);
            return ['success' => false, 'msg' => '充值金額不能小於等於零'];
        }

        $charge_action = $this->userCreditAccountRepository->update_user_credit($charge_amount, $amount_unit, 'increase', $user_id);

        if(!$charge_action['success']){
            $this->userCreditAcOperationRecordRepository->change_record_status_to_failed($record_id);
            return ['success' => false, 'msg' => '儲值失敗'];
        }

        $this->userCreditAcOperationRecordRepository->change_record_status_to_success($record_id);

        $get_user_credit = $this->userCreditAccountRepository->get_user_account($user_id);
        return [
            'success' => true,
            'original_credit' => $charge_action['original_credit'],
            'original_credit_unit' => $charge_action['original_credit_unit'],
            'new_credit' => $get_user_credit['credit'],
            'new_credit_unit' => $get_user_credit['currency_unit']
        ];
    }
//--------------------------------------------------------------------
//  acc_payable contract
//--------------------------------------------------------------------
    public function get_ac_payable_contract_record($role, $role_id, $duration_start,$duration_end){
        $get_record = $this->transactionRepository->get_ac_payable_contract_record($role, $role_id, $duration_start,$duration_end);
        if(!$get_record){
            return ['success' => false];
        }
        return ['success' => true, 'data' => $get_record];
    }

}
?>