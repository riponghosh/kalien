<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $table = 'products';

    public function __construct($attributes = array())
    {
        $this->fillable = $this->create_fillable();
        parent::__construct($attributes);
    }
    public function create_fillable(){
        $fillable_data = [];
        $datas = ['title','sub_title','description','map_address'];
        $lan_zh_tw = '_zh_tw';
        $lan_en = '_en';
        $lan_jp = '_jp';
        foreach ($datas as $data){
            array_push($fillable_data, $data.$lan_zh_tw);
            array_push($fillable_data, $data.$lan_en);
            array_push($fillable_data, $data.$lan_jp);
        }
        //no lang column
        $fillable_data = array_merge($fillable_data,['is_ticket','map_url','pdt_type'] );
        return $fillable_data;
    }

    public function Merchant(){
        return $this->belongsTo('App\Merchant\Merchant', 'merchant_id', 'id');
    }

    public function trip_img(){
        return $this->hasMany('App\TripActivityMedia','trip_activity_id','id','uni_name');
    }
    public function trip_activity_cover(){
        return $this->hasOne('App\TripActivityMedia','trip_activity_id','id')->with('media')->where('is_gallery_image',1);
    }
    public function trip_activity_tickets(){
        return $this->hasMany('App\Models\TripActivityTicket', 'trip_activity_id', 'id');
    }
    public function trip_activity_short_intros(){
        return $this->hasMany('App\Models\Product\TripActivityShortIntro', 'trip_activity_id', 'id');
    }
    public function trip_activity_refund_rules(){
        return $this->hasMany('App\Models\Product\TripActivityRefundRule','trip_activity_id','id');
    }

    public function rule_infos(){
        return $this->hasMany('App\Models\Product\TripActivityRuleInfo','trip_activity_id','id');
    }

    public function customer_rights(){
        return $this->hasMany('App\Models\Product\ProductCustomerRight', 'trip_activity_id', 'id');
    }
    public static function boot()
    {
        parent::boot();
        self::deleting(function($model)
        {
            $model->trip_activity_refund_rules()->delete();
        });
    }
}
