<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivityTicket extends Model
{

    use SoftDeletes;


    protected $table = 'user_activity_tickets';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];



    function Trip_activity_ticket(){
        return $this->hasOne('App\TripActivityTicket','id', 'trip_activity_ticket_id');
    }

    public function user_ta_tickets_incidental_coupons(){
        return $this->hasOne('App\UserTaTicketsIncidentalCoupon', 'parent_ticket_id', 'id');
    }

    public function relate_gp_activity(){
        return $this->hasOne('App\UserActivityTicketUserGpActivity', 'user_activity_ticket_id', 'id');
    }

    public function ticket_refunding(){
        return $this->hasOne('App\UserActivityTicket\TicketRefunding', 'user_activity_ticket_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function($model)
        {
            $model->user_ta_tickets_incidental_coupons()->delete();
            //$model->ticket_refunding()->delete();
        });
    }

}
?>