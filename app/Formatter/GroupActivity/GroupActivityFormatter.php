<?php
namespace  App\Formatter\GroupActivity;

use App\Formatter\Interfaces\IFormatter;

class GroupActivityFormatter implements IFormatter
{
    function __construct()
    {
    }

    public function dataFormat($gp_activity, callable $closure = null)
    {
        if(!$gp_activity)
        {
            return [];
        }


        return (object)[
            'gp_activity_id' => $gp_activity->gp_activity_id,

        ];
    }
}