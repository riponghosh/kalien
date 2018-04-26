<?php

namespace App\Repositories\UserActivityTicket;

use App\UserActivityTicket;
use League\Flysystem\Exception;

class UserActivityTicketRepo
{
    protected $model;

    function __construct(UserActivityTicket $userActivityTicket)
    {
        $this->model = $userActivityTicket;
    }

    function eager_load($model){
        return $model
            ->with('Trip_activity_ticket')
            ->with('Trip_activity_ticket.Trip_activity')
            ->with('ticket_refunding')
            ->with('relate_gp_activity.user_group_activity');
    }

    function create($data){
        return $this->model->create($data);
    }

    function first_by_ticket_id($ticket_id){
        $query = $this->model->where('ticket_id', $ticket_id)->first();

        return $query;
    }

    function get($attr = array()){
        $query = $this->model;
        $query = $this->eager_load($query);
        if(isset($attr['owner_id'])){
            $query = $query->where('owner_id', $attr['owner_id']);
        }
        $query = $query->get();

        return $query;
    }
    function delete_by_model($userActivityTicket){
        $query = $userActivityTicket->delete();
        if(!$query) throw new Exception('失敗。');
        return $query;
    }

}