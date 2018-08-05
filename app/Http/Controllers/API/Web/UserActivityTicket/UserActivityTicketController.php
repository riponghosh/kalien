<?php

namespace App\Http\Controllers\API\Web\UserActivityTicket;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramService;
use App\Services\UserActivityTicket\ActivityTicketService;
use Auth;
use League\Flysystem\Exception;

class UserActivityTicketController extends Controller
{
    protected $userActivityTicketService;

    function __construct(ActivityTicketService $userActivityTicketService)
    {
        $this->userActivityTicketService = $userActivityTicketService;
        parent::__construct();
    }

    function authorized_to(){
        $request = request()->input();
        if(!isset($request['ticket_id'], $request['authorize_to'])) throw new Exception('失敗。');
        $this->userActivityTicketService->authorize_to(Auth::user()->id, $request['authorize_to'], $request['ticket_id']);
        return $this->apiFormatter->success($this->apiModel);
    }

    function use_ticket(TelegramService $telegramService){
        $request = request()->input();
        $action = $this->userActivityTicketService->use_ticket(Auth::user()->id, $request['ticket_id']);
        if (!$action) throw new Exception('', 3);
        if(!empty($product_merchant_telegram_acs = $action->Trip_activity_ticket->Trip_activity->Merchant->telegram_ac)){
            foreach($product_merchant_telegram_acs as $product_merchant_telegram_ac) {
                $ticket_info = '🎫' . $action['sub_title'] . chr(10) . '💵' . $action['amount'] . chr(10) . '👨‍💼' . $action->owner['name'] . chr(10) . '產品名：' . $action['name'].chr(10).'編號'.'PN_180232'.$action['id'];
                try {
                    $telegramService->message_sender($ticket_info, $product_merchant_telegram_ac['account_id']);
                } catch (Exception $e) {

                }
            }
        }
        /*
        if($product_merchant_fb_ac = $action->Trip_activity_ticket->Trip_activity->Merchant->fb_merchant_ac){
            $ticket_info = '🎫'.$action['sub_title'].chr(10).'💵'.$action['amount'].chr(10).'👨‍💼'.$action->owner['name'].chr(10).'產品名：'.$action['name'];
            try{
                $facebookMessengerService->merchant_message_sender([
                    'recipient'		=>	['id'   => $product_merchant_fb_ac['account_id'] ],
                    'message'		=>	['text' => $ticket_info]
                ]);
            }catch (Exception $e){

            }
        }
        */
        $this->apiModel->setData(['activity_name' => $action['name'], 'detail' => $action['sub_title'], 'use_date' => $action['start_date']]);
        return $this->apiFormatter->success($this->apiModel);
    }
}