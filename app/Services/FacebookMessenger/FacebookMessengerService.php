<?php

namespace App\Services\FacebookMessenger;

class FacebookMessengerService
{
    function __construct()
    {
    }

    public function message_sender($response){
        // set our post
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . env('PAGE_ACCESS_TOKEN'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function merchant_message_sender($response){
        // set our post
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . env('PNEKO_MERCHANT_PAGE_ACCESS_TOKEN'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function verifyAccess($request)
    {
        // FACEBOOK_MESSENGER_WEBHOOK_TOKEN is not exist yet.
        // we can set that up in our .env file
        $local_token = env('FACEBOOK_MESSENGER_WEBHOOK_TOKEN');
        $hub_verify_token = $request->hub_verify_token;
        // condition if our local token is equal to hub_verify_token
        if ((string)$hub_verify_token === (string)$local_token) {
            // echo the hub_challenge in able to verify.
            echo $request->hub_challenge;
            exit;
        }
    }
}
?>

