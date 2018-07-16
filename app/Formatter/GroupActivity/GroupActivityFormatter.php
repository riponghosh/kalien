<?php
namespace  App\Formatter\GroupActivity;

use App\Formatter\Interfaces\IFormatter;

class GroupActivityFormatter implements IFormatter
{
    protected $hostFormatter;
    protected $participantFormatter;
    function __construct(HostFormatter $hostFormatter, ParticipantsFormatter $participantsFormatter)
    {
        $this->hostFormatter = $hostFormatter;
        $this->participantsFormatter = $participantsFormatter;
    }

    public function dataFormat($gp_activity, callable $closure = null)
    {
        if(!$gp_activity)
        {
            return [];
        }


        return [
            'gp_activity_id' => $gp_activity->gp_activity_id,
            'activity_title' => $gp_activity->activity_title,
            'product_name' => $gp_activity->trip_activity_ticket->name_zh_tw,
            'duration' => $gp_activity->duration,
            'duration_unit' => $gp_activity->duration_unit,
            'start_date' => $gp_activity->start_date,
            'start_time' => $gp_activity->start_time,
            'timezone' => $gp_activity->timezone,
            'limit_joiner' => $gp_activity->limit_joiner,
            'applicants' => $gp_activity->applicants,
            'participants' => $this->participantsFormatter->dataFormat($gp_activity->applicants),
            'is_achieved' => $gp_activity->is_achieved,
            'forbidden_reason' => $gp_activity->forbidden_reason,
            'joining_fee' => $gp_activity->trip_activity_ticket->amount,
            'joining_fee_unit' => $gp_activity->trip_activity_ticket->currency_unit,
            'host' => $this->hostFormatter->dataFormat($gp_activity->host)
        ];
    }
}