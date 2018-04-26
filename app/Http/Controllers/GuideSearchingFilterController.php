<?php
namespace App\Http\Controllers;


use App\Services\UserGroupActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuideSearchingFilterService;
use App\Services\TripActivityService;
use Illuminate\Support\Facades\Auth;
use App\Services\ChatRoomService;
use App\Repositories\GuideSearchingFilterRepository;

class GuideSearchingFilterController extends Controller
{
	protected $GuideSearchingFilterRepository;
	protected $GuideSearchingFilterService;
	protected $chatRoomService;
	protected $tripActivityService;
	protected $userGroupActivityService;

	public function __construct(GuideSearchingFilterRepository $GuideSearchingFilterRepository, UserGroupActivityService $userGroupActivityService,GuideSearchingFilterService $GuideSearchingFilterService,ChatRoomService $chatRoomService, TripActivityService $tripActivityService)
	{
		$this->GuideSearchingFilterRepository = $GuideSearchingFilterRepository;
		$this->GuideSearchingFilterService = $GuideSearchingFilterService;
		$this->chatRoomService = $chatRoomService;
		$this->tripActivityService = $tripActivityService;
		$this->userGroupActivityService = $userGroupActivityService;
	}

    public function closure_test_page(Request $request)
    {
        $travel_area = isset($request->t_area) ? $this->GuideSearchingFilterService->get_cities_by_region_name($request->t_area) : null;
        $age_range = $request->max_age == '' || $request->min_age ? NULL : array($request->max_age,$request->min_age);
        //$searchResult = $this->GuideSearchingFilterRepository->getFilterResult($request, $travel_area, $age_range);
        $searchResult = null;
        /*客服*/
        $service_room = null;
        /*group activity*/
        $group_activities = $this->userGroupActivityService->get_group_activities();
        if(!Auth::guest()){
            $service_room = $this->chatRoomService->get_customer_service_room_id([Auth::user()->id],true);
            if(!$service_room)  $service_room = null;
        }

        return view('homeForClosure',compact('searchResult','service_room','group_activities'));
    }
	/*篩選條件*/
	public function getFilterResult(Request $request){
		$content = $request['content'];
		/*驗證filter數值*/
		$validator = $this->filter_validator($content);
		/*驗證country_region*/
		if(isset($content['servicePlace']['region']) ){
			if(count($content['servicePlace']['region']) > 0){
				if(!$this->GuideSearchingFilterService
					 	 ->servicePlace_region_validator($content['servicePlace']['country'],
													 $content['servicePlace']['region']
				)) return ['status' => 'error'];
			}
		}
		if( $validator->fails() ) return ['status' => 'error'];
		$searchResult = $this->GuideSearchingFilterRepository
							 ->getFilterResult($content);
		return view('home',compact('searchResult'));
	}	
	public function filter_validator($input){
		$validator = Validator::make($input,[
			'gender.value' => 'in:M,F,both',
			'age.maxValue' => 'integer|Between:15,70', //轉了做max_age
			'age.minValue' => 'integer|Between:15,70',//轉了做min_age
			'servicePlace.country' => 'in:mo,hk,jp,kp,tw',
		]);
		return $validator;
	}
}
?>
