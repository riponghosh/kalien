<?php

namespace App\Repositories\TripActivityTicket;

use App\Repositories\BaseRepository;
use App\Models\TripActivityTicket;

class TripActivityTicketRepo extends BaseRepository
{

    function __construct()
    {

        parent::__construct();
    }

    function model(){
        return new TripActivityTicket();
    }

    function eager_load($model){
        return $model
            ->with('disable_dates')
            ->with('gp_buying_status')
            ->with('Trip_activity');
    }

    function first($id){
        return $this
            ->model->where('id', $id)
            ->first();
    }

    function get($attr = array()){
        $query = $this->model;
        //$query = $this->eager_load($query);
        if(isset($attr['ids'])){
            $query = $query->whereIn('id', $attr['ids']);
        }
        $query = $query->get();
        return $query;
    }
    function get_by_trip_activity($attr = array()){
        $query = $this->model;

        $query->whereHas('Trip_activity', function ($q) use ($attr){
            if(isset($attr['merchant_id'])){
                $q->where('merchant_id', $attr['merchant_id']);
            }
        });

        $query = $query->get();

        return $query;
    }
    function get_by_trip_activity_id($trip_activity_id, $attr = array()){
        $query = $this->model->with('Trip_activity');

        $query = $query->where('trip_activity_id', $trip_activity_id);

        $query->whereHas('Trip_activity', function ($q) use ($attr){
            if(isset($attr['merchant_id'])){
                $q->where('merchant_id', $attr['merchant_id']);
            }
        });

        $query = $query->get();

        return $query;
    }
}