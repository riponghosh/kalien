<?php

namespace App\Services\TripActivity;

use App\Repositories\TripActivity\TripActivityRepo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TripActivityService
{
    protected $repo;

    function __construct(TripActivityRepo $tripActivityRepo)
    {
        $this->repo = $tripActivityRepo;
    }

    function get(array $attr){
        return $this->repo->whereCond($attr)->get();
    }

    function first_by_id($id){
        $trip_activity = $this->repo->findBy($id, 'id');
        return $trip_activity;
    }
    function first_by_uni_name( $uni_name, $attr = array()){
        $trip_activity = $this->repo->findBy($uni_name, 'uni_name');
        return $trip_activity;
    }

    function update($model, $data){
        $query = $model;

        $updateData = static::applyUpdatesToQuery($data);
        //print_r($updateData);
        return $query->update($updateData);
    }

    private static function applyUpdatesToQuery(
         $updates) {
        $output = array();
        
        foreach ($updates as  $updateField => $value){
            $decorator =
                __NAMESPACE__ . '\\Updates\\' .
                str_replace(' ', '', ucwords(
                    str_replace('_', ' ', $updateField)));

            if (class_exists($decorator)) {
                $output =  $decorator::apply($value);
            }
        };
        print_r(json_encode($output));

        return $output;
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