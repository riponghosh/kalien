<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Trip extends Model
{
    use SoftDeletes;

    protected $table = 'trips';
    protected $fillable = ['trip_author','trip_content','trip_status','map_url','map_address','external_link'];
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'trip_id';
    public function trip_media(){
        return $this->hasMany('App\TripMedia')->where('feature_order',1);
    }
}
?>

