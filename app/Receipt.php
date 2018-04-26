<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{

	protected $table = 'receipts';
	protected $fillable = ['*'];
	protected $guarded = ['id'];

	public function guide_service_ticket(){
		return $this->hasOne('App\GuideServiceRequest','id', 'product_id');
	}
	public function trip_activity_ticket(){
	    return $this->hasOne('App\TripActivityTicket','id','product_id');
    }
    public function trip_activity(){
	    return $this->trip_activity_ticket->hasOne('App\Product','id','trip_activity_id');
    }
    public function gp_activity(){
        return $this->hasOne('App\UserGroupActivity\UserGroupActivity', 'gp_activity_id', 'relate_gp_activity_id');
    }
}
?>