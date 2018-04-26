<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTaTicketsIncidentalCoupon extends Model
{
    use SoftDeletes;

    protected $table = 'user_ta_tickets_incidental_coupons';

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    function User_activity_ticket(){
        return $this->hasOne('App\UserActivityTicket', 'id', 'parent_ticket_id');
    }
    

}
?>

