<?php

namespace App\Repositories\TripActivityTicket;

use App\TripActivityTicket;

class TripActivityTicketRepo
{
    protected $model;

    function __construct(TripActivityTicket $tripActivityTicket)
    {
        $this->model = $tripActivityTicket;
    }


    function eager_load($model){
        return $model
            ->with('disable_dates')
            ->with('Trip_activity');
    }

    function first($id){
        return $this
            ->model
            ->find($id);
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