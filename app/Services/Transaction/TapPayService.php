<?php

namespace App\Services\Transaction;

use App\Exceptions\Transaction\TapPay\TapPayPayFail;
use League\Flysystem\Exception;

class TapPayService
{
    function __construct()
    {
    }

    public function pay($tap_pay_req_params){
        $tap_pay_result = [];
        $tap_pay_result = $this->curl_work(env('TAP_PAY_DIRECT_PAY_URL'),$tap_pay_req_params);
        if($tap_pay_result == null){
            throw new TapPayPayFail();
        }
        if(!array_key_exists('status',$tap_pay_result)){
            throw new TapPayPayFail();
        }
        if($tap_pay_result['status'] != 0){
            throw new TapPayPayFail();
        }
        return $tap_pay_result;

    }

    public function refund($refund_amt_twd, $data){
        $data['amount'] = $refund_amt_twd;
        $tap_pay_result = null;
        $tap_pay_result = $this->curl_work(env('TAP_PAY_REFUND_URL'),$data);
        if($tap_pay_result == null){
            return ['success' => false, 'info' => 'no'];
        }
        if(!array_key_exists('status',$tap_pay_result)){
            return ['success' => false, 'info' => 'no status;'.json_encode($tap_pay_result, true)];
        }
        if($tap_pay_result['status'] != 0){
            return ['success' => false, 'info' => json_encode($tap_pay_result, true)];
        }
        return ['success' => true , 'info' => json_encode($tap_pay_result, true)];
    }

    private function curl_work($url, $data){
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($data_string),'x-api-key:'.env('TAP_PAY_PARTNER_KEY')));
        $result = curl_exec($ch);
        $result_array = json_decode($result, true);
        return $result_array;
    }
}