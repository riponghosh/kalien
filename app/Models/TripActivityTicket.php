<?php

namespace App\Models;

use App\Enums\ProductType\ProductTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TripActivityTicket extends Model
{
    use SoftDeletes;

    protected $table = 'trip_activity_tickets';
    protected $fillable;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->fillable = $this->create_fillable();

    }
    public function create_fillable(){
        $fillable_data = [];
        $datas = ['name','description'];
        $lan_zh_tw = '_zh_tw';
        //$lan_en = '_en';
        //$lan_jp = '_jp';
        foreach ($datas as $data){
            array_push($fillable_data, $data.$lan_zh_tw);
            //array_push($fillable_data, $data.$lan_en);
            //array_push($fillable_data, $data.$lan_jp);
        }
        //no lang column
        array_merge($fillable_data,['amount', 'currency_unit', 'qty_unit'] );
        return $fillable_data;
    }

    public function ta_ticket_incidental_coupon(){
        return $this->hasOne('App\TaTicketIncidentalCoupon',  'trip_activity_ticket_id', 'id');
    }

    public function Trip_activity(){
        return $this->belongsTo('App\Models\Product','trip_activity_id','id')->where('pdt_type', ProductTypeEnum::GROUP_ACTIVITY);
    }

    public function fix_time_ranges(){
        return $this->hasOne('App\Models\TripActivityTicket\FixTimeRange', 'trip_activity_ticket_id', 'id');
    }

    public function disable_dates(){
        return $this->hasMany('App\Models\TripActivityTicket\DisableDate', 'activity_ticket_id', 'id');
    }

    public function disable_weeks(){
        return $this->hasMany('App\Models\TripActivityTicket\DisableWeek','activity_ticket_id', 'id');
    }

    public function merchant(){
        return $this->belongsTo('App\Merchant\Merchant', 'merchant_id', 'id');
    }

    public function gp_buying_status(){
        return $this->hasMany('App\Models\TripActivityTicket\GpBuyingStatus', 'trip_activity_ticket_id', 'id');
    }


}
