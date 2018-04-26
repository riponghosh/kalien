<?php

namespace App\Services\Transaction;

use App\Enums\Pay2GoEnum;
use App\Exceptions\Transaction\CreateTwGovReceiptFail;
use GuzzleHttp\Client;
use League\Flysystem\Exception;


class Pay2GoTwGovService
{

    const TAX_RATE = 5;

    function __construct()
    {
    }
    /*
     * Data 範本
      create_data_pay2go_e_invoice(
        $invoice_no,
        [ // inovice
            'type' => ,
            'B2B_id' => ,
        ],
        [ // carry
            'type' => ,
            'code' => ,
        ],
        $donation_code,
        [// buyer
            'name' => ,
            'address' =>  ,
            'email'  => ,
            'phone'  =>
        ]
        $items_arr
      )
       $item = array(
                'name' => '',
                'count' => 1,
                'unit' => '張',
                'price' =>
       );
     */
    public function invoicing($invoice_no, $invoice_type_arr = array(), $invoice_carry_arr = array(), $donation_code, $buyer_info_arr = array(), $items_arr = array()){
        function items_arr_add_amt($items_arr = array()){
            $items_arr = collect($items_arr)->map(function($ar){
                $ar['amt'] = $ar['price']*$ar['count'];
                return $ar;
            });
            return $items_arr;
        }
        function items_convert($items_arr = array()){
            $idxs = ['name', 'count', 'unit', 'price', 'amt'];

            $items_to_string_arr = array();

            foreach ($idxs as $idx){
                $data_to_string = "";
                $data = array_pluck($items_arr, $idx);
                foreach ($data as $k => $v){
                    if($k != 0){
                        $data_to_string .= '|'.$v;
                    }else{
                        $data_to_string = $v;
                    }
                }
                $items_to_string_arr[$idx] = $data_to_string;
            }
            return $items_to_string_arr;

        }
        //====以上為副程式====
        $post_data_array = array();
        $post_data_env = array(
            "RespondType" => "JSON",
            "Version" => "1.1",
            "TimeStamp" => time(),
            "Status" => "1", //1=立即開立，0=待開立，3=延遲開立 "CreateStatusTime" => "",
            "NotifyEmail" => "1", //1=通知，0=不通知
            "PrintFlag" => "Y"
        );
        $post_data_array = array_merge($post_data_array, $post_data_env);

        $post_data_array = array_merge($post_data_array, array(//post_data 欄位資料
            "MerchantOrderNo" => $invoice_no,
            "Comment" => ""
        ));

        $post_data_buyer_info = array(
            "BuyerName" => $buyer_info_arr['name'],
            "BuyerAddress" => $buyer_info_arr['address'],
            "BuyerEmail" => $buyer_info_arr['email'],
            "BuyerPhone" => $buyer_info_arr['phone'],
        );
        $post_data_array = array_merge($post_data_array, $post_data_buyer_info);

        //Category Type
        $post_data_category = array();
        if($invoice_type_arr['type'] == Pay2GoEnum::INVOICE_TYPE_B2C){
            $post_data_category = array(
                "Category" => "B2C"
            );
            //CarryType
            if(in_array($invoice_carry_arr['type'],[(string)Pay2GoEnum::CARRY_TYPE_PHONE_NUM, (string)Pay2GoEnum::CARRY_TYPE_CT_DIGITAL_CERTIFICATE, (string)Pay2GoEnum::CARRY_TYPE_PAY2GO],true))
            {
                //檢查條碼格式
                if(($invoice_carry_arr['type'] === (string)Pay2GoEnum::CARRY_TYPE_PHONE_NUM && $this->carrier_type_phone_valid($invoice_carry_arr['code'])) || ($invoice_carry_arr['type'] === (string)Pay2GoEnum::CARRY_TYPE_CT_DIGITAL_CERTIFICATE && $this->carrier_type_PC_valid($invoice_carry_arr['code']))){
                    $post_data_array["PrintFlag"] = "N";
                    $post_data_array = array_merge($post_data_array,[
                        "CarrierType" => $invoice_carry_arr['type'],
                        "CarrierNum" => rawurlencode($invoice_carry_arr['code'])
                    ]);
                }else{
                    $post_data_array = array_merge($post_data_array, ["CarrierType" => ""]);
                }

            }else{
                $post_data_array = array_merge($post_data_array, ["CarrierType" => ""]);
            }
            //LoveCode
            if(!empty($donation_code) && $this->love_code_valid($donation_code)){
                $post_data_array["PrintFlag"] = "N";
                $post_data_array = array_merge($post_data_array, [
                    "LoveCode" => $donation_code
                ]);
            }
        }elseif($invoice_type_arr['type'] == Pay2GoEnum::INVOICE_TYPE_B2B){
            $post_data_category = array(
                "Category" => "B2B",
                "BuyerUBN" => $invoice_type_arr['B2B_id']
            );
        }else{
            throw new CreateTwGovReceiptFail('錯誤發票種類');
        }
        $post_data_array = array_merge($post_data_array, $post_data_category);

        //Item
        $items_arr = items_arr_add_amt($items_arr);
        $items_str_arr = items_convert($items_arr);
        $post_data_items = array(
            "ItemName" => $items_str_arr['name'], //多項商品時，以「|」分開
            "ItemCount" => $items_str_arr['count'], //多項商品時，以「|」分開 "ItemUnit" => "個|個", //多項商品時，以「|」分開 "ItemPrice" => "300|100", //多項商品時，以「|」分開 "ItemAmt" => "300|200", //多項商品時，以「|」分開 "Comment" => "TEST，備註說明",
            "ItemUnit" => $items_str_arr['unit'],
            "ItemPrice" => $items_str_arr['price'], //多項商品時，以「|」分開 "ItemAmt" => "300|200", //多項商品時，以「|」分開
            "ItemAmt" => $items_str_arr['amt'], //多項商品時，以「|」分開
        );
        $post_data_array = array_merge($post_data_array, $post_data_items);
        //Amount
        $total_amt = $this->cal_total_amount_include_sales_tax_by_items($items_arr);
        $tax_amt = $this->get_total_tax_amt($total_amt);
        $amt = $total_amt - $tax_amt;
        $post_data_amount = array(
            "TaxType" => "1",
            "TaxRate" => self::TAX_RATE,
            "Amt" => (integer)$amt, "TaxAmt" => (integer)$tax_amt, "TotalAmt" => (integer)$total_amt
        );

        $post_data_array = array_merge($post_data_array, $post_data_amount);

        $url = env('PAY2GO_INVOICE_CREATE_URL');
        $result = $this->curl_work($url, $this->build_pay2go_post_data($post_data_array)); //背景送出

        return $result['web_info'];
    }

    public function invoice_invalid($invoice_num, $reason = ""){
        $post_data_array = array(
            "RespondType" => "JSON",
            "Version" => "1.0",
            "TimeStamp" => time(),
            'InvoiceNumber' => $invoice_num,
            'InvalidReason' => $reason
        );
        $url = env('PAY2GO_INVOICE_INVALID_URL');
        $result = $this->curl_work($url, $this->build_pay2go_post_data($post_data_array)); //背景送出
        return $result['web_info'];
    }
//----------------------------------------------------------------------
//
//  副函式
//----------------------------------------------------------------------
    private function build_pay2go_post_data($post_data_array){
        function addpadding($string, $blocksize = 32) {
            $len = strlen($string);
            $pad = $blocksize - ($len % $blocksize);
            $string .= str_repeat(chr($pad), $pad);
            return $string;
        }
        $post_data_str = http_build_query($post_data_array);
        $key = env('PAY2GO_HASH_KEY');
        $iv = env('PAY2GO_HASH_IV');

        $post_data = trim(
            bin2hex(
                openssl_encrypt(
                    addpadding($post_data_str), 'AES-256-CBC',
                    $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv
                )
            )
        );
        $transaction_data_array = array( //送出欄位
            "MerchantID_" => env('PAY2GO_MERCHANT_ID'),
            "PostData_" => $post_data
        );
        return http_build_query($transaction_data_array);
    }

    private function curl_work($url = "", $parameter = ""){
        $curl_options = array(
            CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "Google Bot", CURLOPT_FOLLOWLOCATION => true, CURLOPT_SSL_VERIFYPEER => FALSE, CURLOPT_SSL_VERIFYHOST => FALSE, CURLOPT_POST => "1", CURLOPT_POSTFIELDS => $parameter
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_errno($ch); curl_close($ch);
        $return_info = array(
            "url" => $url,
            "sent_parameter" => $parameter, "http_status" => $retcode, "curl_error_no" => $curl_error, "web_info" => $result
        );
        return $return_info;
    }

    private function love_code_valid($donation_code){
        if(strlen($donation_code) < 3 || strlen($donation_code) > 8) {
            return false;
        }
        if(!is_numeric($donation_code)){
            return false;
        }

        return true;
    }
    private function carrier_type_phone_valid($carrier_num){
        if(strlen($carrier_num) != 8){
            return false;
        }
        if(strpos($carrier_num,'\/') != 0){
            return false;
        };
        return true;
    }

    private function carrier_type_PC_valid($carrier_num){
        if(strlen($carrier_num) != 16){
            return false;
        }
        $characters = str_split($carrier_num, 1);
        for($i = 0; $i <= 15; $i++){
            if($i==0 || $i == 1){ //首兩字是英文
                if(is_numeric($characters[$i])){
                    return false;
                }
            }else{//尾是數字
                if(!is_numeric($characters[$i])){
                    return false;
                }
            }
        }
        return true;


    }
    private function get_total_amt_exclude_sales_tax($amount_include_sales_tax){
        return round(($amount_include_sales_tax/(100+self::TAX_RATE))*100);
    }

    private function get_total_tax_amt($amount_include_sales_tax){
        return round($amount_include_sales_tax*self::TAX_RATE/(100 + self::TAX_RATE));
    }

    private function cal_total_amount_include_sales_tax_by_items($items = array()){
        $amount_include_sales_tax = 0;
        foreach ($items as $item){
            if(!isset($item['price']) || !isset($item['count'])){
                throw new Exception(__FUNCTION__.';'.__CLASS__.';');
            }
            $amount_include_sales_tax += $item['price']*$item['count'];
        }

        return $amount_include_sales_tax;
    }

}