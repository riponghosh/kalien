<?php
namespace  App\Formatter\UserActivityTicket;

use App\Formatter\Interfaces\IFormatter;
use App\Services\UserActivityTicket\ActivityTicketService;

class UserActivityTicketFormatter implements IFormatter
{
    protected $activityTicketService;
    function __construct(ActivityTicketService $activityTicketService)
    {
        $this->activityTicketService = $activityTicketService;
    }

    function dataFormat($data, callable $closure = null)
    {
        if(!$data) return [];

        return [
            'ticket_number' => 'PN_180232'.$data['id'],
            'name' =>  $data['name'],
            'ticket_hash_id' => $data['ticket_id'],
            'sub_title' => $data['sub_title'],
            'start_date' => $data['start_date'],
            'gp_event_start_time' => optional(optional($data['relate_gp_activity'])['user_group_activity'])['start_time'],
            'end_date' => $data['end_date'],
            'relate_gp_activity_id' => optional($data['relate_gp_activity'])['user_gp_activity_id'],
            'authorized_to' => $data['authorized_to'],
            'assignee' => $data['assignee'],
            'is_available' => $data['is_available'],
            'use_duration' => $this->activityTicketService->get_use_duration($data),
            'amt' => $data['amount'],
            'currency_unit' => $data['currency_unit'],
            'used_at' => $data['used_at']
        ];
    }
}