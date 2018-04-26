<?php

namespace App\Repositories\Transaction;

use App\CartReceipt;
use App\Receipt;

class ReceiptRepo
{
    protected $model;
    protected $cartReceipt;

    function __construct(Receipt $receipt, CartReceipt $cartReceipt)
    {
        $this->model = $receipt;
        $this->cartReceipt = $cartReceipt;
    }

    function get_by_user_id($user_id){
        return $this->model->where('user_id', $user_id)->get();
    }

    function delete_by_user_id($user_id){
        $get = $this->model->where('user_id', $user_id)->first();
        if(!$get) return true;
        $del_cart_receipt = $this->cartReceipt->where('user_id', $user_id)->delete();
        return $this->model->where('user_id', $user_id)->delete();
    }
}
