<?php
namespace  App\Formatter\GroupActivity;

use App\Formatter\Interfaces\IFormatter;

class HostFormatter implements IFormatter
{
    function __construct()
    {
    }

    public function dataFormat($host, callable $closure = null){
        if(empty($host)) return null;

        return [
            'id' => $host['id'],
            'name' => $host['name'],
            'sm_avatar' => storageUrl(optional($host->avatar)->media['media_location_low']),
        ];
    }
}