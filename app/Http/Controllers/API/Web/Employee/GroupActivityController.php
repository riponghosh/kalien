<?php
namespace App\Http\Controllers\API\Web\Employee;
use App\Http\Controllers\Controller;
use App\Services\UserGroupActivityService;

class GroupActivityController extends Controller
{
    protected $userGroupActivityService;
    function __construct(UserGroupActivityService $userGroupActivityService)
    {
        $this->userGroupActivityService = $userGroupActivityService;
        parent::__construct();
    }
    /*
     * Pneko成功向非合作商戶購買門票
     */
    function update_gp_activity_has_pdt_stock(){
        $data = request()->input();

        $this->userGroupActivityService->pdt_has_stock($data['gp_activity_id']);

        return $this->apiFormatter->success($this->apiModel);
    }
}
?>