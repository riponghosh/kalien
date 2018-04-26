<?php
namespace  App\Formatter\TripActivity;

use App\Formatter\Interfaces\IFormatter;

class TicketIncidentalCouponsFormatter implements IFormatter
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
        return (object)[
            "id" => $data->id,
            "amount" => $data->amount,
            "amount_unit" => $data->amount_unit
        ];
    }
}