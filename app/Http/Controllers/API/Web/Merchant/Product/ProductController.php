<?php

namespace App\Http\Controllers\API\Web\Merchant\Product;

use App\Formatter\Merchant\TripActivitiesFormatter;
use App\Http\Controllers\Controller;
use App\Services\TripActivity\TripActivityService;
use Auth;
use League\Flysystem\Exception;

class ProductController extends Controller
{

    protected $tripActivityService;
    function __construct(TripActivityService $tripActivityService)
    {
        $this->tripActivityService = $tripActivityService;
        parent::__construct();
    }

    function get(TripActivitiesFormatter $tripActivitiesFormatter){
        $trip_activities = $this->tripActivityService->get([
            'merchant_id' => Auth::user()->id
        ]);

        $data = $tripActivitiesFormatter->dataFormat($trip_activities);
        $this->apiModel->setData($data);
        return $this->apiFormatter->success($this->apiModel);
    }

}