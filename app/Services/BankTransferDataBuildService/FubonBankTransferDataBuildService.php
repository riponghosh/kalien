<?php

namespace App\Services\BankTransferDataBuildService;

use Carbon\Carbon;

class FubonBankTransferDataBuildService extends BankTransferDataBuildService
{
    function __construct()
    {
    }

    function create_transfer_header($transaction_date, $transaction_rows_num, $total_amt_twd){
        $opt_string ='';

        //add head
        $opt_string .= 'H';

        $opt_string .= $transaction_date;
        $opt_string .= $transaction_rows_num;
        $opt_string .= $this->zero_fill_out($this->total_transfer_amt_converter($total_amt_twd,2),'l', 14);
        return $opt_string;
    }
    function bank_data_converter($data, &$err_report = array()){
        $out_put_string = '';
        foreach ($data as $d){
            $string = '';
            //first string
            $string .= 'C';
            if(($bank_code_string = $this->bank_code_converter($d['bank_code'])) === false) {
                $err_report[] = [
                    'err' => 'bank code',
                    'data' => $d
                ];
                continue;
            }
            $string .= $bank_code_string;

            if(($account_number_string = $this->account_number_converter($d['account_number'])) === false){
                $err_report[] = [
                    'err' => 'bank account number',
                    'data' => $d
                ];
                continue;
            }
            $string .= $this->zero_fill_out($account_number_string,'l', 16);

            if(($transfer_amt_string = $this->transfer_amt_converter($d['amt'], 2)) === false){
                $err_report[] = [
                    'err' => 'amt',
                    'data' => $d
                ];
                continue;
            }
            $string .= $this->zero_fill_out($transfer_amt_string,'l', 14);

            if(($acc_name_string = $this->acc_name_converter($d['acc_name'])) === false){
                $err_report[] = [
                    'err' => 'account name',
                    'data' => $d
                ];
                continue;
            }
            $string .= $this->space_fill_out($acc_name_string,'r', 10);

            if(($email_string = $this->email_converter($d['email'],101)) === false){
                $err_report[] = [
                    'err' => 'email',
                    'data' => $d
                ];
                continue;
            }
            $string .= $this->space_fill_out($email_string, 'r', 101);

            if(($mail_content_string = $this->mail_content_converter($d['mail_content'],100)) === false){
                $err_report[] = [
                    'err' => 'mail content',
                    'data' => $d
                ];
                continue;
            }
            $string .= $this->space_fill_out($mail_content_string, 'r', 100);

            $out_put_string .= $string.chr(10);

        }
        return $out_put_string;
    }

    public function bank_code_converter($bank_code){
        if(strlen($bank_code) != 3 || !is_numeric($bank_code)) return false;

        return $bank_code;
    }

    public function account_number_converter($account_number){
        if(strlen($account_number) > 16 || !is_numeric($account_number)) return false;
        return $account_number;
    }

    public function total_transfer_amt_converter($amt, $decimal){
        $amt_string = '';
        $amt_string = sprintf("%1\$.".$decimal."f",$amt);

        $amt_split = explode(".", (string)$amt_string);

        $amt_string = $amt_split[0].$amt_split[1];

        return $amt_string;
    }

    public function transfer_amt_converter($amt, $decimal){
        $amt_string = '';
        $amt_string = sprintf("%1\$.".$decimal."f",$amt);

        $amt_split = explode(".", (string)$amt_string);

        $amt_string = $amt_split[0].$amt_split[1];

        return $amt_string;
    }

    public function acc_name_converter($acc_name){
        if(strlen($acc_name) > 10) return false;

        return $acc_name;
    }

    public function email_converter($email, $max_length){
        if(strlen($email) > $max_length) return false;
        return $email;
    }

    public function mail_content_converter($mail_content, $max_length){
        if(strlen($mail_content) > $max_length) return false;
        return $mail_content;
    }

}