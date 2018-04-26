<?php
namespace App\Http\Controllers\API\Web\ActivityTicket;

use App\Http\Controllers\Controller;
use League\Flysystem\Exception;
use App\Services\TripActivityTicket\TripActivityTicketService;

class ActivityTicketController extends Controller
{
    protected $tripActivityTicketService;
    function __construct(TripActivityTicketService $tripActivityTicketService)
    {
        $this->tripActivityTicketService = $tripActivityTicketService;
        parent::__construct();
    }

    function get_activity_ticket_all_sold_date_and_time_ranges()
    {
        $request = request()->input();
        if(empty($request['trip_activity_ticket_id'])) throw new Exception();
        $ticket = $this->tripActivityTicketService->get_by_id($request['trip_activity_ticket_id']);
        $black_lists_dates = $this->tripActivityTicketService->get_blacklist_dates($ticket);
        $start_time_ranges = $this->tripActivityTicketService->get_start_time_ranges($ticket);


        $this->apiModel->setData(['sold_dates' => $black_lists_dates,'time_ranges' => $start_time_ranges]);
        return $this->apiFormatter->success($this->apiModel);
    }
}