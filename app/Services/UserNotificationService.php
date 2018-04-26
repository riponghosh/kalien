<?php
namespace App\Services;

use App\Repositories\UserNotificationRepository;

class UserNotificationService
{

    protected $userNotificationRepository;

    function __construct(UserNotificationRepository $userNotificationRepository)
    {
        $this->userNotificationRepository = $userNotificationRepository;
    }

    function get_all_notifications($user_id){
        $data = $this->userNotificationRepository->get_all_notifications_by_user_id($user_id);
        if(!$data) return ['success' => false];
        $data = $data->toArray();
        foreach ($data as $k => $v){
            array_forget($data[$k], 'user_id');
        }
        return ['success' => true,'data' => $data];
    }

    function update_notificaton_read_status($notification_id, $user_id, $status = true){
        if(!$this->userNotificationRepository->update_is_read($notification_id, $user_id, $status)) return ['success' => false];
        return ['success' => true];
    }
}
?>

