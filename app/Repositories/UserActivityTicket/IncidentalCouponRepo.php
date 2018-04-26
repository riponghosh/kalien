<?php

namespace App\Repositories\UserActivityTicket;

use App\UserTaTicketsIncidentalCoupon;

class IncidentalCouponRepo
{
    protected $model;

    function __construct(UserTaTicketsIncidentalCoupon $userTaTicketsIncidentalCoupon)
    {
        $this->model = $userTaTicketsIncidentalCoupon;
    }

    function create($data){
        return $this->model->create($data);
    }
}
?>