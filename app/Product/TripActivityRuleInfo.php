<?php
namespace App\Product;

use Illuminate\Database\Eloquent\Model;

class TripActivityRuleInfo extends Model
{
    protected $table = 'trip_activity_rule_infos';

    function rule_type(){
        return $this->hasOne('App\Product\TripActivityRuleInfoType', 'id', 'info_id');
    }
}