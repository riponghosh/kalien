<?php
namespace  App\Formatter\Employee\GroupActivity;

use App\Formatter\Interfaces\IFormatter;

class ParticipantFormatter implements IFormatter
{
    function __construct()
    {
    }

    public function dataFormat($participant, callable $closure = null){
        if(empty($participant)) return null;

        return [
            'name' => $participant->user['name'],
            'sm_avatar' => storageUrl(optional(optional($participant->user)->avatar)->media['media_location_low']),
        ];
    }
}