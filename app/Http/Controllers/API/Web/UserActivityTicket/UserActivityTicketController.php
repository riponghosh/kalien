<?php

namespace App\Http\Controllers\API\Web\UserActivityTicket;

use App\Http\Controllers\Controller;
use App\Services\UserActivityTicket\ActivityTicketService;
use Auth;
use League\Flysystem\Exception;

class UserActivityTicketController extends Controller
{
    protected $userActivityTicketService;

    function __construct(ActivityTicketService $userActivityTicketService)
    {
        $this->userActivityTicketService = $userActivityTicketService;
        parent::__construct();
    }

    function use_ticket(){
        $request = request()->input();
        $action = $this->userActivityTicketService->use_ticket(Auth::user()->id, $request['ticket_id']);
        if (!$action) throw new Exception('', 3);

        $this->apiModel->setData(['activity_name' => $action['name'], 'detail' => $action['sub_title'], 'use_date' => $action['start_date']]);
        return $this->apiFormatter->success($this->apiModel);
    }
}