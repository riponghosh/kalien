<?php
namespace App\Services\Transaction;

use App\Enums\Pay2GoEnum;
use App\Exceptions\Transaction\CreateTwGovReceiptFail;
use App\Repositories\Transaction\TwGovReceiptRepo;

class TwGovReceiptService
{
    protected $repo;

    function __construct(TwGovReceiptRepo $twGovReceiptRepo)
    {
        $this->repo = $twGovReceiptRepo;
    }

    public function create($invoice_id, $invoice_type, $B2B_id = null, $receipt_carry_type, $receipt_carry_num = null,  $donation_code = null, $invoice_mail_address = null){
        //資料檢查
        if(!in_array($invoice_type,[Pay2GoEnum::INVOICE_TYPE_B2C,Pay2GoEnum::INVOICE_TYPE_B2B])) {
            throw new CreateTwGovReceiptFail();
        }
        if($invoice_type == Pay2GoEnum::INVOICE_TYPE_B2B && $B2B_id == null){
            throw new CreateTwGovReceiptFail();
        }
        //載具資料
        if(in_array($receipt_carry_type,[(string)Pay2GoEnum::CARRY_TYPE_PHONE_NUM,(string)Pay2GoEnum::CARRY_TYPE_CT_DIGITAL_CERTIFICATE,(string)Pay2GoEnum::CARRY_TYPE_PAY2GO],true)){
            if($receipt_carry_num == null){
                throw new CreateTwGovReceiptFail();
            }
        }else{
            $receipt_carry_type = null;
        }
        $add_gov_receipt_info = $this->repo->create($invoice_id, $invoice_type, $B2B_id, $receipt_carry_type, $receipt_carry_num, $donation_code, $invoice_mail_address, $attr = array());

        return $add_gov_receipt_info;
    }

    function add_pay2go_invoicing_response_data($id, $raw_data)
    {
        $data = json_decode($raw_data, true);
        $update_data = ['gov_receipt_response_data' => $raw_data];

        $update_data['pay2go_status'] = isset($data['Status']) ? $data['Status'] : null;
        if(isset($data['Result'])){
            if(is_string($data['Result'])){
                $result = json_decode($data['Result'],true);
            }elseif(is_array($data['Result'])){
                $result = $data['Result'];
            }else{
                $result = [];
            }
            $update_data['pay2go_check_code'] = isset($result['CheckCode']) ? $result['CheckCode'] : null;
            isset($result['InvoiceNumber']) ? $update_data['gov_receipt_number'] = $result['InvoiceNumber'] : null;
        }

        //紀録
        $this->repo->operate_record_invoicing(
            $id, [
                'data' => $raw_data,
                'pay2go_status' => $update_data['pay2go_status'],
        ]);
        return $this->repo->update($id, $update_data);
    }

    function add_pay2go_invaliding_response_data($id, $raw_data)
    {
        $data = json_decode($raw_data, true);


        $this->repo->operate_record_invaliding(
            $id, [
            'data' => $raw_data,
            'pay2go_status' => isset($data['Status']) ? $data['Status'] : null,
        ]);

        return $this->repo->update($id, ['invalid_at' => date('Y-m-d H:i:s')]);
    }
}