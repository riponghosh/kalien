<?php

namespace App\Repositories;

use App\Invoice;
use App\Invoice\TwGovReceipt;

class GovInvoiceRepository
{
    protected $InvoiceTwGovReceipt;
    function __construct(Invoice $invoice , TwGovReceipt $twGovReceipt)
    {
        $this->InvoiceTwGovReceipt = $twGovReceipt;
    }

    function create_tw_gov_receipt($invoice_id, $gov_receipt_type, $B2B_id = null, $receipt_carry_type, $receipt_carry_num = null, $is_donated, $donation_code = null, $gov_receipt_mail_address = null, $attr = array()){
        $data = [
            'invoice_id' => $invoice_id,
            'gov_receipt_type' => $gov_receipt_type,
            'mail_address' => $gov_receipt_mail_address,
        ];
        if($gov_receipt_type == 0){ //B2C
            //捐贈
            $data['is_donate'] = $is_donated;
            if($is_donated == true){
                 $data['donation_code'] = $donation_code;
            }
            //載具  0:無，1:自然人，2:手機條碼
            $data['receipt_carry_type'] =  $receipt_carry_type;
            if($receipt_carry_type != 0){
                $data['receipt_carry_num'] = $receipt_carry_num;
            }
        }elseif($gov_receipt_type == 2){ //B2B
            if($B2B_id == null){
                return ['success' => false];
            }else{
                $data['B2B_tax_id'] = $B2B_id;
            }
        }else{
            return ['success' => false];
        }

        $create = $this->InvoiceTwGovReceipt->create($data);
        if(!$create) return ['success' => false];
        return ['success' => true];
    }
}
?>