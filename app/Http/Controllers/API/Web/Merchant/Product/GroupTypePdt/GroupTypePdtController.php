<?php
/*
*  GroupTypePdt 是 pdt_type = 2
*
*/
namespace App\Http\Controllers\API\Web\Merchant\Product\GroupTypePdt;

use App\Http\Controllers\Controller;
use App\Services\TripActivityTicket\TripActivityTicketService;
use League\Flysystem\Exception;
use Auth;

class GroupTypePdtController extends Controller
{
    protected $tripActivityTicketService;
    function __construct(TripActivityTicketService $tripActivityTicketService)
    {
        $this->tripActivityTicketService = $tripActivityTicketService;
        parent::__construct();
    }

    /**
     * @api {post} /api-merchant/v1/product/disable_sale_date/add
     *
     * */
    function add_disable_date(){
        $request = request()->input();
        if(!$request['trip_activity_ticket_id']) throw new Exception('trip_activity_ticket_id is required',2);

        $action = $this->tripActivityTicketService->add_disable_dates(2, Auth::user()->id, $request['trip_activity_ticket_id'], $request['disable_dates']);

        return $this->apiFormatter->success($this->apiModel);

    }

    function delete_disable_date(){
        $request = request()->input();
        if(!$request['trip_activity_ticket_id']) throw new Exception('trip_activity_ticket_id is required',2);

        try{
            $action = $this->tripActivityTicketService->delete_disable_dates(2, Auth::user()->id, $request['trip_activity_ticket_id'], $request['disable_dates']);

        }catch (Exception $e){
            $this->apiModel->setMsg($e->getMessage());
            $this->apiModel->setCode(2);
            return $this->apiFormatter->error($this->apiModel);
        }

        return $this->apiFormatter->success($this->apiModel);

    }
}
?>