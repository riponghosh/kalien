<?php
namespace App\Http\Controllers;

use App\Formatter\UserActivityTicket\UserActivityTicketFormatter;
use App\Repositories\UserActivityTicket\UserActivityTicketRepo;
use App\Services\UserActivityTicket\ActivityTicketService;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

class HashTicketController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function use_ticket_by_hash($ticket_hash_id, Request $request, UserActivityTicketRepo $userActivityTicketRepo, ActivityTicketService $userActivityTicketService, UserActivityTicketFormatter $userActivityTicketFormatter){

        $result = [
            'success' => false
        ];
        if(!$ticket_hash_id){
            return abort(404);
        };
        $ticket = $userActivityTicketRepo->findBy($ticket_hash_id,'ticket_id');
        if(!$ticket){
            return view('TicketUseResult', compact('result'));
        }
        $ticket['is_available'] = $userActivityTicketService->helper_ticket_is_available($ticket);
        $ticket = $userActivityTicketFormatter->dataFormat($ticket);
        $result['success'] = true;
        return view('TicketUseResult',compact('ticket','result'));
    }
}