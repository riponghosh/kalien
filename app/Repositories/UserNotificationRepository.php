<?php

namespace App\Repositories;

use App\UserNotification;

class UserNotificationRepository
{
    protected $userNotification;

    function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification;
    }

    function get_all_notifications_by_user_id($user_id){
        return $this->userNotification->where('user_id', $user_id)->get();
    }

    function update_is_read($id ,$user_id, $status){
        return $this->userNotification->where('user_id', $user_id)->where('id', $id)->update(['is_read' => $status]);
    }
}
?>