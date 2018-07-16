<?php

namespace App\Models\TripActivityTicket;

use Illuminate\Database\Eloquent\Model;

class GpBuyingStatus extends Model
{
    protected $table = 'gp_buying_status';

    protected $guarded = ['id'];

    function media(){
        return $this->morphMany('App\Media', 'mediable');
    }

}