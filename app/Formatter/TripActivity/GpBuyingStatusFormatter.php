<?php
namespace  App\Formatter\TripActivity;

use App\Formatter\Interfaces\IFormatter;

class GpBuyingStatusFormatter implements IFormatter
{
    /*
     * 範例
        "id": 4,
        "name_zh_tw": "2小時",
        "description_zh_tw": "專人教練",
        "amount": 500,
        "currency_unit": "TWD",
        "qty_unit": "2",
        "trip_activity_id": 7,
        "merchant_id": 3,
        "created_at": null,
        "updated_at": null,
        "deleted_at": null,
        "restrict_qty_per_day": null,
        "available": 1,
        "qty_unit_type": "hour",
        "min_participant_for_gp_activity": 5,
        "max_participant_for_gp_activity": 14,
        "has_time_ranges": 1,
        "time_range_restrict_group_num_per_day": 1
     */
    protected $ticketIncidentalCouponsFormatter;

    public function __construct()
    {

    }

    public function dataFormat($data, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$data)
        {
            return [];
        }

        return $data->map(function($gp_buying_status){

            return (object)[
                'name' => $gp_buying_status->name,
                'desc' => $gp_buying_status->desc,
                'people_amt' => $gp_buying_status->people_amt,
                'media' => $this->mediaFormat($gp_buying_status->media),

            ];
        });
    }

    public function mediaFormat($data){
        return $data->map(function ($d){
            return (object)[
                'url' => storageUrl($d->media_location_standard)
            ];
        });
    }

}