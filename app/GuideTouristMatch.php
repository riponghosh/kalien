<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideTouristMatch extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'guide_tourist_matches';
    protected $fillable = ['guide_id', 'status', 'tourist_id','date_start','date_end','schedule_id','is_expired'];

    public function userGuide(){
    	return $this->belongsTo('App\User','guide_id','id');
    }

    public function userTourist(){
    	return $this->hasOne('App\User','id','tourist_id');
    }

    public function appointmentDates(){
        return $this->hasMany('App\AppointmentDate','guide_tourist_matches_id','id');
    }
}
