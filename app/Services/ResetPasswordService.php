<?php
namespace App\Services;

use App\EmailAPI;
use App\AccessAPI;
use Illuminate\Support\Facades\DB;


class ResetPasswordService
{
    public function __construct()
    {
    }

    public function send_mail($email, $token){
        $emailAPI = new EmailAPI(new AccessAPI());
        return $emailAPI->reset_password($email, env('APP_URL').'/password/reset/'.$token);
    }
}
?>

