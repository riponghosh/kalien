<?php
namespace  App\Formatter\TripActivity;

use App\Formatter\Interfaces\IFormatter;

class PackageDisableWeekFormatter implements IFormatter
{
    public function dataFormat($data, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$data)
        {
            return [];
        }

        /*
* 			    "id": 3,
                "trip_activity_ticket_id": 3,
                "amount": 25,
                "amount_unit": "TWD",
                "created_at": null,
                "updated_at": null
         */
        return array_pluck($data, 'week');
    }
}