<?php
namespace  App\Formatter\Employee;

use App\Formatter\Interfaces\IFormatter;

class GpBuyingStatusFormatter implements IFormatter
{
    
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
                'id' => $gp_buying_status->id,
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