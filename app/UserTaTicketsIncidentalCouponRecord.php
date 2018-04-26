<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTaTicketsIncidentalCouponRecord extends Model
{
    use SoftDeletes;

    protected $table = 'user_ta_tickets_incidental_coupon_records';

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];
}
?>