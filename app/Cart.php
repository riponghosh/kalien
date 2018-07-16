<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $guarded = ['id'];

    public function trip_activity_ticket(){
        return $this->hasOne('App\Models\TripActivityTicket','id','product_id');
    }
    public function trip_activity(){
        return $this->trip_activity_ticket->hasOne('App\Models\Product','id','trip_activity_id');
    }
}