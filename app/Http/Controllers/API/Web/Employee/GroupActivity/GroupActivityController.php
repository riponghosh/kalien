<?php
namespace App\Http\Controllers\API\Web\Employee\GroupActivity;
use App\Formatter\Employee\GroupActivity\GroupActivityFormatter;
use App\Http\Controllers\Controller;
use App\QueryFilters\Employee\GroupActivity\GroupActivitySearch;
use App\Services\UserGroupActivityService;
use Illuminate\Http\Request;

class GroupActivityController extends Controller
{
    protected $userGroupActivityService;

    function __construct(UserGroupActivityService $userGroupActivityService)
    {
        $this->userGroupActivityService = $userGroupActivityService;
        parent::__construct();
    }

    public function get(Request $request, GroupActivityFormatter $groupActivityFormatter){
        $result = GroupActivitySearch::apply($request);
        $gp_activities_collection = collect($result);
        $gp_activities_collection->map(function($item, $key) use ($groupActivityFormatter){
            if(empty($item->achieved_at)){
                $item = $this->userGroupActivityService->get_forbidden_reason($item);
            }

            return $groupActivityFormatter->dataFormat($item);
        });
        $data = $gp_activities_collection->toArray();
        $this->apiModel->setData($data);
        return $this->apiFormatter->success($this->apiModel);
    }

    function update_gp_activity_has_pdt_stock(){
        $data = request()->input();

        $this->userGroupActivityService->pdt_has_stock($data['gp_activity_id']);

        return $this->apiFormatter->success($this->apiModel);
    }

}
?>

