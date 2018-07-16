<?php
namespace  App\Formatter\Employee\GroupActivity;

use App\Formatter\Interfaces\IFormatter;

class ParticipantsFormatter implements IFormatter
{
    protected $participantFormatter;
    function __construct(ParticipantFormatter $participantFormatter)
    {
        $this->participantFormatter = $participantFormatter;
    }

    function dataFormat($participants, callable $closure = null)
    {
        if(!$participants)
        {
            return [];
        }

        return $participants->map(function($participant){
            return $this->participantFormatter->dataFormat($participant);
        });
    }
}