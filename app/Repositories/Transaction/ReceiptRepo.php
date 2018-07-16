<?php

namespace App\Repositories\Transaction;

use App\CartReceipt;
use App\Receipt;
use App\Repositories\BaseRepository;

class ReceiptRepo extends BaseRepository
{
    protected $model;
    protected $cartReceipt;

    function __construct(CartReceipt $cartReceipt)
    {
        $this->cartReceipt = $cartReceipt;

        parent::__construct();
    }

    function model()
    {
        return new Receipt();
    }

    function eagerLoad()
    {
        $this->model
            ->with('guide_service_ticket')->with('guide_service_ticket.seller')
            ->with('trip_activity_ticket')->with('trip_activity_ticket.trip_activity');
    }

    function delete_by_user_id($user_id){
        $get = $this->model->where('user_id', $user_id)->first();
        if(!$get) return true;
        $del_cart_receipt = $this->cartReceipt->where('user_id', $user_id)->delete();
        return $this->model->where('user_id', $user_id)->delete();
    }
}
