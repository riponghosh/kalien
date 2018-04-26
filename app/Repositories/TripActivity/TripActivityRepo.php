<?php

namespace App\Repositories\TripActivity;

use App\Enums\ProductType\ProductTypeEnum;
use App\Product;

class TripActivityRepo
{
    protected $model;

    function __construct(Product $product)
    {
        $this->model = $product;
    }

    function get($attr = array()){
        $query = $this->model->with('trip_activity_tickets');
        if(isset($attr['merchant_id'])){
            $query = $query->where('merchant_id', $attr['merchant_id']);
        }

        $query = $query->get();

        return $query;
    }

    function first_by_uni_name($uni_name, $attr = array()){
        return $this->model->where('uni_name', $uni_name)->first();
    }

    function create($data = array()){
        $data['pdt_type'] = ProductTypeEnum::GROUP_ACTIVITY;
        return $this->model->create($data);
    }
}
