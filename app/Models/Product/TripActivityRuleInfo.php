<?php
namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class TripActivityRuleInfo extends Model
{
    protected $table = 'trip_activity_rule_infos';

    function rule_type(){
        return $this->hasOne('App\Models\Product\TripActivityRuleInfoType', 'id', 'info_id');
    }
}