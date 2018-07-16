<?php
namespace App\Http\Controllers;

use App\Formatter\TripActivity\TripActivityTicketsFormatter;
use App\Formatter\TripActivity\TripActivityFormatter;
use App\Services\TripActivity\TripActivityService;
use Illuminate\Http\Request;

class TripActivityController extends Controller
{
    protected $tripActivityService;
    function __construct(TripActivityService $tripActivityService)
    {
        $this->tripActivityService = $tripActivityService;
    }

    function get_trip_activity(Request $request, TripActivityFormatter $tripActivityFormatter, TripActivityTicketsFormatter $tripActivityTicketsFormatter){
        if(!isset($request->activity_uni_name)) return abort(404);
        $get_trip_activity = $this->tripActivityService->first_by_uni_name($request->activity_uni_name);
        if(!$get_trip_activity) return abort(404);

        $trip_activity = $tripActivityFormatter->dataFormat($get_trip_activity);
        $trip_activity_tickets = $trip_activity['trip_activity_tickets'];

        return view('tripActivity/tripActivity', compact('trip_activity', 'trip_activity_tickets'));
    }

    function get_trip_activity_mobile(Request $request, TripActivityFormatter $tripActivityFormatter, TripActivityTicketsFormatter $tripActivityTicketsFormatter){
        if(!isset($request->activity_uni_name)) return abort(404);
        $get_trip_activity = $this->tripActivityService->first_by_uni_name($request->activity_uni_name);
        if(!$get_trip_activity) return abort(404);

        $trip_activity = $tripActivityFormatter->dataFormat($get_trip_activity);
        $trip_activity_tickets = $trip_activity['trip_activity_tickets'];

        return view('mobiles.product', compact('trip_activity', 'trip_activity_tickets'));
    }
}
?>