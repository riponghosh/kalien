<?php

namespace App;

use App\AccessAPI;

class EmailAPI
{

    protected $accessApi;

    function __construct(AccessAPI $accessApi) {
        $this->accessApi = $accessApi;
    }

    function mail_confirm($email_address, $confirm_url) {
        $data = array(
            "service" => "mail_confirm",
            "hostname" => "email.pneko",
            "data" => array(
                "email_address" => $email_address,
				"confirm_url" => $confirm_url,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

	    function reset_password($email_address, $reset_url) {
        $data = array(
            "service" => "reset_password",
            "hostname" => "email.pneko",
            "data" => array(
                "email_address" => $email_address,
				"reset_url" => $reset_url,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function refunded_fail_noti($email_address, $ticket_id , $ticket_type, $refunded_at, $err_msg){
        $data = array(
            "service" => "refund_failed_noti",
            "hostname" => "email.pneko",
            "data" => array(
                "email_address" => $email_address,
                'ticket_id' => $ticket_id,
                'ticket_type' => $ticket_type,
                'refunded_at' => $refunded_at,
                'err_msg' => $err_msg
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function group_achieved_noti_for_merchant($email_address, $product_name, $start_date, $start_time, $gp_activity_id, $gp_activity_url){
        $data = array(
            "service" => "refund_failed_noti",
            "hostname" => "email.pneko",
            "data" => array(
                "email_address" => $email_address,
                'product_name' => $product_name,
                'start_date' => $start_date,
                'start_time' => $start_time,
                'gp_activity_id' => $gp_activity_id,
                'gp_activity_url' => $gp_activity_url
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

}
?>

