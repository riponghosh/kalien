<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class TripMedia extends Model
{
    protected $table = 'trips_media';
    protected $fillable = ['trip_id','media_id','feature_order','media_type'];
    protected $primaryKey = 'trips_media_id';

    public function media(){
        return $this->hasOne('App\Media','media_id','media_id');
    }
}
?>

