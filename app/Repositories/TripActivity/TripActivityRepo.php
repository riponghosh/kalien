<?php

namespace App\Repositories\TripActivity;

use App\Enums\ProductType\ProductTypeEnum;
use App\Models\Product;
use App\Repositories\BaseRepository;

class TripActivityRepo extends BaseRepository
{
    function __construct()
    {
        parent::__construct();
    }

    function model()
    {
        return new Product();
    }

    function eagerLoad(){
        $this->model = $this->model->with(
            'trip_activity_tickets',
            'trip_activity_tickets.gp_buying_status',
            'rule_infos.rule_type',
            'trip_activity_short_intros',
            'trip_img',
            'trip_img.media'
        );
    }


    function create($data = array()){
        $data['pdt_type'] = ProductTypeEnum::GROUP_ACTIVITY;
        return $this->model->create($data);
    }
}
