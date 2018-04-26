<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserNotificationService;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    protected $userNotificationService;
    function __construct(UserNotificationService $userNotificationService)
    {
        $this->userNotificationService = $userNotificationService;
    }

    function get_all_notifications(Request $request){
        $result = $this->userNotificationService->get_all_notifications(Auth::user()->id);
        if(!$result['success']) return ['success' => false];
        return ['success' => true, 'data' => $result['data']];

    }
    function update_notification_to_read(Request $request){
        $result = $this->userNotificationService->update_notificaton_read_status($request->notification_id, Auth::user()->id);

        return $result;
    }
}
?>