<?php

namespace App\Repositories\Transaction;

use App\Invoice\TwGovReceipt;
use App\Transaction\TwGovReceipt\TwGovReceiptOperateRecords;

class TwGovReceiptRepo
{
    const OPERATE_REC_TYPE_INVOICING = 1;
    const OPERATE_REC_TYPE_INVALIDING = 2;
    protected $model;
    protected $opreate_record;

    function __construct(TwGovReceipt $twGovReceipt, TwGovReceiptOperateRecords $twGovReceiptOperateRecords)
    {
        $this->model = $twGovReceipt;
        $this->opreate_record = $twGovReceiptOperateRecords;
    }

    function create($invoice_id, $gov_receipt_type, $B2B_id = null, $receipt_carry_type, $receipt_carry_num = null, $donation_code = null, $gov_receipt_mail_address = null, $attr = array())
    {
        $data = [
            'invoice_id' => $invoice_id,
            'gov_receipt_type' => $gov_receipt_type,
            'mail_address' => $gov_receipt_mail_address,
        ];
        if($gov_receipt_type == 0){ //B2C
            //捐贈
            $data['donation_code'] = $donation_code;
            //載具  0:無，1:自然人，2:手機條碼
            $data['receipt_carry_type'] =  $receipt_carry_type;
            $data['receipt_carry_num'] = $receipt_carry_num;
        }elseif($gov_receipt_type == 2){ //B2B
            if($B2B_id == null){
                return ['success' => false];
            }else{
                $data['B2B_tax_id'] = $B2B_id;
            }
        }else{
            return ['success' => false];
        }

        $create = $this->model->create($data);

        return $create;
    }

    function update($id, $data)
    {
        return $this->model->find($id)->update($data);
    }

    function operate_record_invoicing($tw_gov_receipt_id, $data = array()){
        $create_data = array(
            'operated_type' => self::OPERATE_REC_TYPE_INVOICING,
            'tw_gov_receipt_id' => $tw_gov_receipt_id,
            'pay2go_status' => $data['data'],
            'pay2go_response' => $data['pay2go_status']
        );
        return $this->opreate_record->create($create_data);
    }

    function operate_record_invaliding($tw_gov_receipt_id, $data = array()){
        $create_data = array(
            'operated_type' => self::OPERATE_REC_TYPE_INVALIDING,
            'tw_gov_receipt_id' => $tw_gov_receipt_id,
            'pay2go_status' => $data['data'],
            'pay2go_response' => $data['pay2go_status']
        );
        return $this->opreate_record->create($create_data);
    }
}