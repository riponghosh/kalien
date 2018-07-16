<?php

namespace App\Repositories;

use App\CartReceipt;
use App\Services\UserActivityTicket\ActivityTicketService;
use Carbon\Carbon;
use App\Enums\AcPayableContract\AcPayableContractRecordEnum;
use App\Enums\ProductTicketTypeEnum;
use App\GuideServiceRequest;
use App\Cart;
use App\Invoice;
use App\InvoiceItem;
use App\AccountPayableContract;
use App\AccountPayableContractRecord\AccountPayableContractRecord;
use App\Receipt;
use App\Services\AcPayableContract\AcPayableContractService;
use App\Models\TripActivityTicket;
use App\UserActivityTicket;
use App\UserActivityTicketUserGpActivity;
use App\UserServiceTicket;
use App\TransactionRelation;
use App\Repositories\ErrorLogRepository;
use League\Flysystem\Exception;

class TransactionRepository
{
	const CLASS_NAME = __CLASS__;

	const TIC_TYPE_US_SERVICE = 1;
	const TIC_TYPE_US_ACTIVITY = 2;
	const ACC_PAYABLE_AFTER_BALANCE_DATE = 2;

	protected $acPayableContractService;
	protected $userActivityTicketService;
	protected $cart;
	protected $tripActivityTicket;
	protected $userActivityTicket;
	protected $receipt;
	protected $cartReceipt;
	protected $err_log;
	protected $guideServiceRequest;

	public function __construct(ActivityTicketService $userActivityTicketService, AcPayableContractService $acPayableContractService, Cart $cart, Receipt $receipt, CartReceipt $cartReceipt, UserServiceTicket $userServiceTicket, UserActivityTicket $userActivityTicket,TripActivityTicket $tripActivityTicket, GuideServiceRequest $guideServiceRequest,ErrorLogRepository $errorLogRepository)
	{
	    $this->userActivityTicketService = $userActivityTicketService; //TODO REMOVE
	    $this->acPayableContractService = $acPayableContractService; //TODO REMOVE
	    $this->cart = $cart;
		$this->receipt = $receipt;
		$this->cartReceipt = $cartReceipt;
		$this->tripActivityTicket = $tripActivityTicket;
		$this->userActivityTicket = $userActivityTicket;
		$this->userServiceTicket = $userServiceTicket;
		$this->guideServiceRequest = $guideServiceRequest;

		$this->err_log = $errorLogRepository;
	}
//-------------------------------------------------------------------
//  Cart
//-------------------------------------------------------------------
	public function create_cart_items($user_id, $data){
        $insert_data = array();
	    foreach ($data as $k => $v){

            $insert_data[$k]['buyer_id'] = $user_id;
            $insert_data[$k]['qty'] = $data[$k]['qty'];
            $insert_data[$k]['product_id'] = $data[$k]['ticket_id'];
            $insert_data[$k]['start_date'] = $data[$k]['start_date'];
            $insert_data[$k]['created_at'] = date('Y-m-d H:i:s');
            $insert_data[$k]['updated_at'] = date('Y-m-d H:i:s');

        }
        $result = $this->cart->insert($insert_data);
        if(!$result) return false;

        return true;
    }

    public function get_all_cart_items_by_user_id($user_id){
	    $query = $this->cart->where('buyer_id', $user_id)->get();
	    return $query;
    }

    public function get_cart_items_by_id($user_id, $cart_ids = array()){
        $query = $this->cart->where('buyer_id', $user_id)->whereIn('id', $cart_ids)->get();
        return $query;
    }

    public function del_cart_by_id($cart_id, $user_id){
        $query = $this->cart->where('id', $cart_id)->where('buyer_id', $user_id)->delete();
        return $query;
    }
//-------------------------------------------------------------------
//  Create Receipt
//-------------------------------------------------------------------
	/*product_type[1 => 'user_service_ticket']*/
	public function create_user_services_receipt($user_id, $data){
		foreach ($data as $k => $v){
			$data[$k]['user_id'] = $user_id;
			$data[$k]['qty'] = 1;
			$data[$k]['created_at'] = date('Y-m-d H:i:s');
			$data[$k]['updated_at'] = date('Y-m-d H:i:s');
			$data[$k]['product_type'] = self::TIC_TYPE_US_SERVICE;
		}
		$result = $this->receipt->insert($data);

		if(!$result) return ['success' => false];

		return ['success' => true];
	}

	public function create_activity_tickets_receipt($user_id, $tickets, $transfer_by_cart = false){
        foreach ($tickets as $k => $ticket){
            $has_qty_limit = $this->tripActivityTicket->find($ticket['ticket_id']);
            if($has_qty_limit->restrict_qty_per_day != null){
             $already_sell_tickets = $this->userActivityTicket->where('start_date', $ticket->start_date)->count();
             if($has_qty_limit->restrict_qty_per_day - $already_sell_tickets - $ticket->qty < 0){
                 $this->err_log->err('over '.$ticket->start_date.'limit qty', self::CLASS_NAME, __FUNCTION__);
                 return ['success' => false];
             }
            }
            $data = array();
            $data['user_id'] = $user_id;
            $data['qty'] = $ticket['qty'];
            $data['start_date'] = $ticket['start_date'];
            $data['start_time'] = isset($ticket['start_time']) ? $ticket['start_time'] : null;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['product_id'] = $ticket['ticket_id'];
            $data['product_type'] = self::TIC_TYPE_US_ACTIVITY;
            if(isset($ticket['transfer_incidental_coupon_to_user_id'])){
                $data['transfer_incidental_coupon_to_user_id'] = $ticket['transfer_incidental_coupon_to_user_id'];
            }
            if(isset($ticket['relate_gp_activity_id'])){
                $data['relate_gp_activity_id'] = $ticket['relate_gp_activity_id'];
            }

            $result = $this->receipt->insertGetId($data);

            if(!$result) return ['success' => false];
            //如果是從cart轉移過來,要寫入cart_receipt
            if($transfer_by_cart == true){
                $this->cartReceipt->create(['user_id' => $user_id, 'cart_id' => $ticket['cart_id'], 'receipt_id' => $result]);
            }

        }

        return ['success' => true];
    }

	public function delete_cart_and_receipts_by_rts_id_and_create_invoice_and_user_tickets_and_ac_payable_contract($user_id, $receipts_item = array(), $total_payment_info){
        $get_user_services_receipts = isset($receipts_item[ProductTicketTypeEnum::USER_SERVICE_TICKET]) ? $receipts_item[ProductTicketTypeEnum::USER_SERVICE_TICKET] : null;
        $get_activity_ticket_receipts = isset($receipts_item[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET]) ? $receipts_item[ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET] : null;
    //--------------------------------------------------
    //  init
    //--------------------------------------------------
        if(empty($get_user_services_receipts) && empty($get_activity_ticket_receipts)){
            $this->err_log->err('ref1', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }
    //---------------------------------------------------
    //create_ticket
    //---------------------------------------------------
        $transaction_relations = array();
        $invoice_items = array();
        $ticket_ids = array();  //hash  [兩票種混合]
        $ac_payables = array();
        $gp_activity_and_joiners = array(); //票券購買成功若是跟活動相連會自動加入活動
        //---------------------------------------------------
        // trip_activity
        //---------------------------------------------------
        if($get_activity_ticket_receipts && count($get_activity_ticket_receipts) > 0){
            $new_activity_tickets = array();
            foreach ($get_activity_ticket_receipts as $receipt){
                for($i = 1; $i<= $receipt->qty; $i++){
                    $ac_payable = array();
                    $activity_ticket = array();
                    $activity_ticket['owner_id'] = $user_id;
                    $activity_ticket['amount'] = $receipt->trip_activity_ticket->amount;
                    $activity_ticket['currency_unit'] = $receipt->trip_activity_ticket->currency_unit;
                    $activity_ticket['trip_activity_ticket_id'] = $receipt->trip_activity_ticket->id;
                    $activity_ticket['start_date'] = $receipt->start_date;
                    $activity_ticket['end_date'] = $receipt->start_date;
                    //新增名稱
                    $tic_name = lan_convert($receipt->trip_activity, 'title') != null ? lan_convert($receipt->trip_activity, 'title') : null;
                    $tic_name = lan_convert($receipt->trip_activity, 'sub_title') != null ?  $tic_name.'-'.lan_convert($receipt->trip_activity, 'sub_title') : $tic_name;
                    $activity_ticket['name'] = $tic_name;
                    $activity_ticket['sub_title'] = lan_convert($receipt->trip_activity_ticket, 'name'); //此項是明細：如1小時
                    //折價券
                    if(!empty($receipt->transfer_incidental_coupon_to_user_id)){
                        $activity_ticket['incidental_coupon_beneficiary'] = $receipt->transfer_incidental_coupon_to_user_id;
                    }

                    $user_activity_ticket = $this->userActivityTicketService->create($activity_ticket);
                    array_push($new_activity_tickets, $activity_ticket);
                    array_push($ticket_ids, $user_activity_ticket['ticket_id']);
                    //for invoice
                    array_push($invoice_items, [
                        'product_id' => $user_activity_ticket['ticket_id'],
                        'qty' => 1,
                        'amount' => $receipt->trip_activity_ticket->amount,
                        'currency_unit' => $receipt->trip_activity_ticket->currency_unit
                    ]);
                    //for account payable
                    $ac_payable = [
                        'user_id' => $user_id,
                        'merchant_id' => $receipt->trip_activity_ticket->merchant_id,
                        'ori_amount' => $receipt->trip_activity_ticket->amount,
                        'currency_unit' => $receipt->trip_activity_ticket->currency_unit,
                        'pneko_charge_plan' => $receipt->trip_activity_ticket->Trip_activity->Merchant->pneko_charge_plan,
                        'product_id' => $user_activity_ticket['ticket_id'],
                        'other_fee_currency_unit' => $receipt->trip_activity_ticket->currency_unit,
                        'description' => null,
                        'pd_use_start_date' => Carbon::createFromFormat('Y-m-d H:i:s', $activity_ticket['start_date'].' '.'00:00:00')->tz($receipt->trip_activity->time_zone)->toDateTimeString(),
                        'end_date' => $activity_ticket['end_date'],
                    ];
                    $pdt_payment_attrs = $total_payment_info['pdt_payment_methods'][(string)$receipt->id];
                    $ac_payable['payment_methods'] = array();
                    foreach ($pdt_payment_attrs['payment_method'] as $pdt_payment_method){
                        if($pdt_payment_method['amt'] != null){
                            $ac_payable['payment_methods'][] = [
                                'payment_type' => $pdt_payment_method['type'],
                                'amount' => $pdt_payment_method['amt'],
                                'amount_unit' => CLIENT_CUR_UNIT
                            ];
                        }
                    }
                    $ac_payable['refund_rules'] = array();
                    //檢查有否refund_rules
                    /*
                    * 沒有商家合作強制成團即不能退款
                    */
                    if($receipt['trip_activity']['merchant_id'] == env('MERCHANT_ID_PNEKO')){
                        $ac_payable['refund_rules'][] = [
                            'refund_before_day' => null,
                            'refund_percentage' => 0,
                            'purchase_at_any_time' => true
                        ];
                    }

                    if(count($receipt['trip_activity']['trip_activity_refund_rules'])){
                        foreach ($receipt['trip_activity']['trip_activity_refund_rules'] as $k => $trip_activity_refund_rule)
                        $ac_payable['refund_rules'][$k] = [
                            'refund_before_day' => $trip_activity_refund_rule['refund_before_day'],
                            'refund_percentage' => $trip_activity_refund_rule['refund_percentage'],
                            'purchase_at_any_time' => $trip_activity_refund_rule['purchase_at_any_time']
                        ];
                    }
                    array_push($ac_payables,$ac_payable);
                    $transaction_relations[$user_activity_ticket['ticket_id']] = [
                        'product_id' => $user_activity_ticket['ticket_id'],
                        'product_type' => self::TIC_TYPE_US_ACTIVITY
                    ];
                    //for gp activity
                    if($receipt->relate_gp_activity_id != null){
                        $group_data = [
                            'gp_activity_id' => $receipt->relate_gp_activity_id,
                            'ticket_id' => $user_activity_ticket['id'],  //持有票卷id
                            'pdt_ticket_id' => $user_activity_ticket['trip_activity_ticket_id'], //產品原本id
                            'ticket_type' => ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET,
                            'group_apply_to_achieved' => false
                        ];
                        //有否限制票量和最少成團人數
                        if($receipt->trip_activity_ticket->time_range_restrict_group_num_per_day != null) {
                            $group_data['group_apply_to_achieved'] = true;
                        }
                        array_push($gp_activity_and_joiners,$group_data);
                    }

                }
            }
            //Insert user_activity_ticket_user__activity
            foreach ($gp_activity_and_joiners as $v){
                if($v['ticket_type'] == ProductTicketTypeEnum::TRIP_ACTIVITY_TICKET){
                    $query = $this->userActivityTicket->where('id', $v['ticket_id'])->first();
                    if(!$query){
                        $this->err_log->err('insert user_activity_ticket_gp_activity fail1', self::CLASS_NAME, __FUNCTION__);
                        return ['success' => false];
                    }

                    $insert_query = UserActivityTicketUserGpActivity::create([
                        'user_activity_ticket_id' => $query['id'],
                        'user_gp_activity_id' => $v['gp_activity_id']
                    ]);

                    if(!$insert_query){
                        $this->err_log->err('insert user_activity_ticket_gp_activity fail2', self::CLASS_NAME, __FUNCTION__);
                        return ['success' => false];
                    }
                }
            }

        }
        //---------------------------------------------------
        //user_service
        //---------------------------------------------------
        if($get_user_services_receipts && count($get_user_services_receipts) > 0){
            $new_user_tickets = array();
            $guide_service_request_type_ids = array();
            foreach ($get_user_services_receipts as $receipt){
                $user_ticket = array();
                $user_ticket['created_at'] = date('Y-m-d H:i:s');
                $user_ticket['updated_at'] = date('Y-m-d H:i:s');
                $user_ticket['ticket_id'] = hash('ripemd160', $user_id.date('Y-m-d H:i:s').$receipt->id.$receipt->product_type);
                $user_ticket['owner_id'] = $user_id;
                $user_ticket['amount'] = $receipt->guide_service_ticket->price_by_seller;
                $user_ticket['currency_unit'] = $receipt->guide_service_ticket->price_unit_by_seller;
                $user_ticket['servicer_id'] = $receipt->guide_service_ticket->request_to_id;
                $user_ticket['start_date'] = $receipt->guide_service_ticket->start_date;
                $user_ticket['end_date'] = $receipt->guide_service_ticket->end_date;
                $user_ticket['start_time'] = $receipt->guide_service_ticket->start_time;
                $user_ticket['end_time'] = $receipt->guide_service_ticket->end_time;
                $user_ticket['service_type'] =  $receipt->guide_service_ticket->service_type;
                //evblock
                $user_ticket['evblock_id'] = $receipt->guide_service_ticket->product_id;
                $user_ticket['evblock_name'] = $receipt->guide_service_ticket->product_name;
                $user_ticket['relate_schedule_id'] = $receipt->guide_service_ticket->relate_schedule_id;

                array_push($new_user_tickets, $user_ticket);
                array_push($ticket_ids, $user_ticket['ticket_id']);
                //for delete
                array_push($guide_service_request_type_ids, $receipt->product_id);
                //for invoice
                array_push($invoice_items, ['product_id' => $user_ticket['ticket_id'], 'qty' => 1,'amount' => $receipt->guide_service_ticket->price_by_seller,'currency_unit' => $receipt->guide_service_ticket->price_unit_by_seller]);
                //for account payable
                array_push($ac_payables,[
                    'seller_id' => $receipt->guide_service_ticket->request_to_id,
                    'ori_amount' => $receipt->guide_service_ticket->price_by_seller,
                    'currency_unit' => $receipt->guide_service_ticket->price_unit_by_seller,
                    'pneko_fee_percentage' => env('RATE_OF_PNEKO_USER_TICKET'),
                    'product_id' => $user_ticket['ticket_id'],
                    'other_fee' => env('RATE_OF_TAPPAY_FEE'),
                    'other_fee_currency_unit' => $receipt->guide_service_ticket->price_unit_by_seller,
                    'is_paid' => false,
                    'description' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'settlement_time' => date('Y-m-d',strtotime("+".self::ACC_PAYABLE_AFTER_BALANCE_DATE." days", strtotime($user_ticket['end_date']))),
                ]);
                $transaction_relations[$user_ticket['ticket_id']] = [
                    'product_id' => $user_ticket['ticket_id'],
                    'product_type' => self::TIC_TYPE_US_SERVICE
                ];

            }
            $insert_query = $this->userServiceTicket->insert($new_user_tickets);
            if(!$insert_query){
                $this->err_log->err('ref2', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }
        }
    //---------------------------------------------------
    // Delete cart
    //---------------------------------------------------
        //---------------------------------------------------
        // trip_activity
        //---------------------------------------------------
        $get_all_receipt_cart = $this->cartReceipt->where('user_id', $user_id)->get();
        if($get_all_receipt_cart && count($get_all_receipt_cart) > 0){
            $cart_ids = array_pluck($get_all_receipt_cart, 'cart_id');
            $del_query = $this->cart->whereIn('id', $cart_ids)->where('buyer_id', $user_id)->delete();

            if(!$del_query){
                $this->err_log->err(' Delete cart fail', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }
        }
        //---------------------------------------------------
        //user_service
        //---------------------------------------------------
        if(isset($guide_service_request_type_ids)) {
            $delete_guide_service_requests = $this->guideServiceRequest->whereIn('id', $guide_service_request_type_ids)->delete();
            if (!$delete_guide_service_requests) {
                $this->err_log->err('ref3', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }
        }
    //---------------------------------------------------
    // Create invoice
    //---------------------------------------------------
        $create_invoice = new Invoice();
        $new_invoice_id = $create_invoice->insertGetId([
            'owner_id' => $user_id,
            'invoice_id' => hash('ripemd160', $user_id.date('Y-m-d H:i:s').'invoice'),
            'total_amount' => $total_payment_info['amount'],
            'currency_unit' => $total_payment_info['currency'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        if(!$new_invoice_id){
            $this->err_log->err('ref5', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }

        foreach ($invoice_items as $k => $invoice_item){
            $product_id = $invoice_item['product_id'];
            unset($invoice_item['product_id']);

            $invoice_item['invoice_id'] =$new_invoice_id;
            $invoice_item_id = InvoiceItem::insertGetId($invoice_item);
            if(!$invoice_item_id){
                $this->err_log->err('ref6', self::CLASS_NAME, __FUNCTION__);
                return ['success' => false];
            }
            $transaction_relations[$product_id]['invoice_item_id'] = $invoice_item_id;
        }
    //---------------------------------------------------
    // Create accounts payable
    //---------------------------------------------------
        foreach ($ac_payables as $k => $ac_payable){
            $product_id = $ac_payable['product_id'];
            unset($ac_payable['product_id']);
            $create_ac_payables_id = $this->acPayableContractService->create_and_get_id($ac_payable);
            $transaction_relations[$product_id]['account_payable_contract_id'] = $create_ac_payables_id;
        }
    //---------------------------------------------------
    // Create Transaction Relations
    //---------------------------------------------------
        $create_transaction_relation = TransactionRelation::insert($transaction_relations);
        if(!$create_transaction_relation){
            $this->err_log->err('ref8', self::CLASS_NAME, __FUNCTION__);
            return ['success' => false];
        }
    //---------------------------------------------------
    // Finish
    //---------------------------------------------------
        return [
            'success' => true,
            'ticket_ids' => $ticket_ids,
            'invoice_id' => $new_invoice_id,
            'gp_activity_and_joiners' => $gp_activity_and_joiners,
        ];
    }//End delete_cart_and_receipts_by_rts_id_and_create_invoice_and_user_tickets_and_ac_payable_contract

    public function update_invoice_tap_pay_info($invoice_id, $user_id, $third_party_res){
        $new_invoice = Invoice::where('owner_id',$user_id)->where('id', $invoice_id)->update([
            'refund_id_tappay' => $third_party_res['rec_trade_id'],
            'tappay_record' => json_encode($third_party_res),
        ]);

        return $new_invoice;
    }

//-------------------------------------------------------------------
// Account Payable Contract
//
//-------------------------------------------------------------------
    public function get_merchant_ac_payable_contracts_record($merchant_id, $settlement_start_date, $settlement_end_date){
        $query = AccountPayableContract::where('merchant_id',$merchant_id)
            ->whereDate('settlement_time','>=' ,$settlement_start_date)->whereDate('settlement_time' ,'<=', $settlement_end_date);
        $query = $query->get();

        return $query;
    }
    public function update_user_ac_payable_contracts_to_balanced_at(){
        //-------------------------------------------------------
        // 取出符合條件的合約，用時間戳記録
        //-------------------------------------------------------
        $msg = '';
        $balance_time = date("Y-m-d H:i:s");
        $now = date("Y-m-d H:i:s");
        $sql_1 = AccountPayableContract::where('settlement_time', '<=', $now)
            ->where('is_paid', false)
            ->where('complain', false)
            ->whereNotNull('seller_id')
            ->whereNull('balanced_at')
            ->update([
                'is_paid' => true,
                'balanced_at' => $balance_time
            ]);

        if(!$sql_1){
            return ['success' => true, 'msg' => $msg.'no payable need transfer.', 'data' => null];
        }
        //-------------------------------------------------------
        // 取出符合條件的合約(用剛時間戳作標記，因update同get不能同時使用)
        //-------------------------------------------------------
        $sql_2 = AccountPayableContract::with('transaction_relation')->where('balanced_at', $balance_time)->get();
        if(count($sql_2) == 0) return ['success' => true, 'data' => null];

        //-------------------------------------------------------
        // 取出product_type是us_service,計算最後結帳。
        //-------------------------------------------------------
        $user_service_ticket_ids_check = array();
        $user_service_ticket_ids_check_hash = array();
        foreach ($sql_2 as $data){
            if($data->transaction_relation->product_type == self::TIC_TYPE_US_SERVICE){
                $user_service_ticket_ids_check_hash[$data->transaction_relation->product_id] = [
                    'id' => $data->id,
                    'final_transfer_amount' => $data->ori_amount - $data->ori_amount*($data->pneko_fee_percentage + $data->other_fee)*0.01,
                    'final_transfer_amount_unit' => $data->currency_unit,
                    'seller_id' => $data->seller_id
                ];
                array_push($user_service_ticket_ids_check, $data->transaction_relation->product_id);
            }else{
                $this->err_log->err('unknown ac_payable: '.$data->id,self::CLASS_NAME, __FUNCTION__);
            }
        }
        //-------------------------------------------------------
        // 產品需要過期(=已使用)
        //-------------------------------------------------------
        $sql_3 = UserServiceTicket::whereIn('ticket_id', $user_service_ticket_ids_check)->where('end_date','<', $now)->get();

        $not_pass_ac_payable_id = array();
        foreach ($sql_3 as $data){
            if(!in_array($data->ticket_id, $user_service_ticket_ids_check)){
                array_push($not_pass_ac_payable_id, $user_service_ticket_ids_check_hash[$data->ticket_id]['id']);
                unset($user_service_ticket_ids_check_hash[$data->ticket_id]);
            }
        }
        //-------------------------------------------------------
        // 只取消未使用產品的合約
        //-------------------------------------------------------
        if(count($not_pass_ac_payable_id) != 0){
            $sql_4 = AccountPayableContract::whereIn('id', $not_pass_ac_payable_id)->update(['is_paid' => false,'balanced_at' => null]);
            if(!$sql_4) return ['success' => false];
            $this->err_log->err('fail acc payable balanced ids: '.json_encode($not_pass_ac_payable_id), self::CLASS_NAME, __FUNCTION__);
            $msg = $msg.'fail to balances ids: '.json_encode($not_pass_ac_payable_id).'; ';
        }

        return ['success' => true, 'msg' => $msg, 'data' => $user_service_ticket_ids_check_hash];


    }

    public function update_merchant_ac_payable_contracts_to_balanced_at(){
        $msg = '';
        $balance_time = date("Y-m-d H:i:s");
        $now = date("Y-m-d H:i:s");
        //-------------------------------------------------------
        // 取出符合條件的合約，用時間戳記録
        //-------------------------------------------------------
        $sql_1 = AccountPayableContract::where('settlement_time', '<=', $now)
            ->where('is_paid', false)
            ->where('complain', false)
            ->whereNotNull('merchant_id')
            ->whereNull('balanced_at')
            ->update([
                'is_paid' => true,
                'balanced_at' => $balance_time
        ]);

        if(!$sql_1){
            return ['success' => true, 'msg' => $msg.'no payable need transfer.', 'data' => null];
        }
        //-------------------------------------------------------
        // 取出符合條件的合約(用剛時間戳作標記，因update同get不能同時使用)
        //-------------------------------------------------------
        $sql_2 = AccountPayableContract::with('transaction_relation')->where('balanced_at', $balance_time)->get();
        if(count($sql_2) == 0) return ['success' => true, 'data' => null];
        //-------------------------------------------------------
        // 取出product_type是us_service,計算最後結帳。
        //-------------------------------------------------------
        $activity_ticket_ids_check = array();
        $activity_ticket_ids_check_hash = array();
        foreach ($sql_2 as $data){
            if($data->transaction_relation->product_type == self::TIC_TYPE_US_ACTIVITY){
                //Amount
                $Pneko_fee = pneko_fee($data->ori_amount,$data->pneko_fee_percentage, $data->currency_unit);

                $other_fee = $data->ori_amount*$data->other_fee*0.01;
                $merchant_revenue = $data->ori_amount - $Pneko_fee - $other_fee;

                $activity_ticket_ids_check_hash[$data->transaction_relation->product_id] = [
                    'id' => $data->id,
                    'final_transfer_amount' => $data->ori_amount - $Pneko_fee - $other_fee,
                    'final_transfer_amount_unit' => $data->currency_unit,
                    'merchant_id' => $data->merchant_id
                ];
                //--------------------------------------
                // Recording
                //--------------------------------------
                //acPayable record : pneko fee
                AccountPayableContractRecord::create([
                    'account_payable_contract_id' => $data->id,
                    'role_type' => 3,
                    'role_id' => null,
                    'd_c' => 1,
                    'amount' => $Pneko_fee,
                    'amount_unit' => $activity_ticket_ids_check_hash[$data->transaction_relation->product_id]['final_transfer_amount_unit'],
                    'action_code' => 3,
                ]);
                $role_type = null;
                if($data['seller_id'] != null){
                    $role_type = 1;
                }elseif ($data['merchant_id'] != null){
                    $role_type = 2;
                }else{
                    $this->err_log->err('undefined role type '.$data->id,self::CLASS_NAME, __FUNCTION__);
                }

                //acPayable record : bank fee
                AccountPayableContractRecord::create([
                    'account_payable_contract_id' => $data->id,
                    'role_type' => 3,
                    'role_id' => null,
                    'd_c' => 1,
                    'amount' => $other_fee,
                    'amount_unit' => $activity_ticket_ids_check_hash[$data->transaction_relation->product_id]['final_transfer_amount_unit'],
                    'action_code' => 4,
                ]);
                //acPayable record : Merchant Revenue
                AccountPayableContractRecord::create([
                    'account_payable_contract_id' => $data->id,
                    'role_type' => $role_type,
                    'role_id' => $data['merchant_id'],
                    'd_c' => 1,
                    'amount' => $merchant_revenue,
                    'amount_unit' => $activity_ticket_ids_check_hash[$data->transaction_relation->product_id]['final_transfer_amount_unit'],
                    'action_code' => 5,
                ]);

                array_push($activity_ticket_ids_check, $data->transaction_relation->product_id);
            }else{
                $this->err_log->err('unknown ac_payable: '.$data->id,self::CLASS_NAME, __FUNCTION__);
            }
        }
        //-------------------------------------------------------
        // 產品需要過期(=已使用)
        //-------------------------------------------------------
        $sql_3 = UserActivityTicket::whereIn('ticket_id', $activity_ticket_ids_check)->where('end_date','<', $now)->get();

        $not_pass_ac_payable_id = array();
        foreach ($sql_3 as $data){
            if(!in_array($data->ticket_id, $activity_ticket_ids_check)){
                array_push($not_pass_ac_payable_id, $activity_ticket_ids_check_hash[$data->ticket_id]['id']);
                unset($activity_ticket_ids_check_hash[$data->ticket_id]);
            }
        }
        //-------------------------------------------------------
        // 只取消未使用產品的合約
        //-------------------------------------------------------
        if(count($not_pass_ac_payable_id) != 0){
            $sql_4 = AccountPayableContract::whereIn('id', $not_pass_ac_payable_id)->update(['is_paid' => false,'balanced_at' => null]);
            if(!$sql_4) return ['success' => false];
            $this->err_log->err('fail acc payable balanced ids: '.json_encode($not_pass_ac_payable_id), self::CLASS_NAME, __FUNCTION__);
            $msg = $msg.'fail to balances ids: '.json_encode($not_pass_ac_payable_id).'; ';
        }

        return ['success' => true, 'msg' => $msg, 'data' => $activity_ticket_ids_check_hash];

    }
//--------------------------------------------------------------------------------------------------------
//  Ac payable Contract record
//--------------------------------------------------------------------------------------------------------

    public function get_ac_payable_contract_record($role, $role_id = null, $duration_start, $duration_end){
        $query = AccountPayableContractRecord::whereDate('created_at','>=', $duration_start)->whereDate('created_at','<=',$duration_end);

        if($role == 3){
            $query->where('role_type',3);
        }elseif($role == 2 || $role == 1){
            $query->where('role_id', $role_id)->where('role_type', $role);
        }

        $query = $query->get();

        return $query;
    }
}

?>