<?php

namespace App\Services\TripActivity;

use App\Repositories\TripActivity\TripActivityRepo;

class TripActivityService
{
    protected $repo;

    function __construct(TripActivityRepo $tripActivityRepo)
    {
        $this->repo = $tripActivityRepo;
    }

    function get($attr = array()){
        return $this->repo->get($attr);
    }

    function first_by_uni_name( $uni_name, $attr = array()){
        return $this->repo->first_by_uni_name($uni_name, $attr);
    }

    function create($data = array()){
        return $this->repo->create($data);
    }
//------------------------------------------------------------------------
//
//   Helpers
//
//------------------------------------------------------------------------
}

?>