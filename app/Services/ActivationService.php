<?php

namespace App\Services;
use App\Repositories\UserProfileRepository;
use App\User;
use App\EmailAPI;

class ActivationService
{

    protected $emailAPI;
    protected $userProfileRepository;
    public function __construct(EmailAPI $emailAPI, UserProfileRepository $userProfileRepository)
    {
        $this->emailAPI = $emailAPI;
        $this->userProfileRepository = $userProfileRepository;
    }
    public function email_activate($activate_code, $uni_name){
        $query = User::where('activate_code',$activate_code)->where('uni_name', $uni_name)->update(['is_activated' => 1]);
        if(!$query) return false;

        return true;
    }
    public function send_confirm_mail($email_address, $activate_code, $uni_name){
        if(env('APP_ENV') != 'production') return ['success' => true];
        $url = 'www.pneko.com/activation/'.$uni_name.'/'.$activate_code;
        $this->emailAPI->mail_confirm($email_address, $url);

        return ['success' => true];
    }

    public function recreate_activate_code($user_id){
        $activate_code = sha1('addSalt'.time().'forConfirm'.$user_id);

        $update_activate_code = $this->userProfileRepository->update_current_user(['activate_code' => $activate_code]);

        if(!$update_activate_code){
            return ['success' => false];
        }

        return ['success' => true, 'activate_code' => $activate_code];
    }
}
?>

