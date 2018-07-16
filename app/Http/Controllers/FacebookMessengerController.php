<?php

namespace App\Http\Controllers;

use App\Models\FbMessagerRecord;
use App\Services\FacebookMessenger\FacebookMessengerService;
use Illuminate\Http\Request;

use App\Http\Requests;
use League\Flysystem\Exception;

class FacebookMessengerController extends Controller
{
    protected $facebookMessengerService;
    public function __construct(FacebookMessengerService $facebookMessengerService)
    {
        $this->facebookMessengerService = $facebookMessengerService;
    }

    public function auto_reply(Request $request)
    {
        // here we can verify the webhook.
        // i create a method for that.
        try{
            $input   = json_decode(file_get_contents('php://input'), true);
            $recipient_id 	 = $input['entry'][0]['messaging'][0]['sender']['id'];
            if(isset($input['entry'][0]['messaging'][0]['message'])){
                $message = $input['entry'][0]['messaging'][0]['message']['text'];
                $response = [
                    'recipient'		=>	['id'   => $recipient_id ],
                    'message'		=>	['text' => 'Hello! :)']
                ];
                $this->facebookMessengerService->message_sender($response);
                FbMessagerRecord::create([
                    'recipient_id' => $recipient_id,
                    'msg' => $message,
                    'response' => json_encode($response,true)
                ]);
            }else{
                exit;
            }


        }catch (Exception $e){
            exit;
        }

    }

    public function merchant_auto_reply(Request $request){
        // here we can verify the webhook.
        // i create a method for that.
        try{
            $input   = json_decode(file_get_contents('php://input'), true);
            $recipient_id 	 = $input['entry'][0]['messaging'][0]['sender']['id'];
            if(isset($input['entry'][0]['messaging'][0]['message'])){
                $message = $input['entry'][0]['messaging'][0]['message']['text'];
                $response = [
                    'recipient'		=>	['id'   => $recipient_id ],
                    'message'		=>	['text' => 'Hello! :)']
                ];
                FbMessagerRecord::create([
                    'recipient_id' => $recipient_id,
                    'msg' => $message,
                    'response' => json_encode($response,true)
                ]);
                $this->facebookMessengerService->merchant_message_sender($response);
            }else{
                exit;
            }


        }catch (Exception $e){
            exit;
        }
    }

    public function verify_web_hook(Request $request){
        $this->facebookMessengerService->verifyAccess($request);
    }

}
