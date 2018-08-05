<?php
namespace App\Http\Controllers;

use App\Formatter\UserActivityTicket\UserActivityTicketFormatter;
use App\Repositories\UserActivityTicket\UserActivityTicketRepo;
use App\Services\Telegram\TelegramService;
use App\Services\UserActivityTicket\ActivityTicketService;
use App\UserActivityTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class HashTicketController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function use_ticket_by_hash($ticket_hash_id, Request $request, TelegramService $telegramService, UserActivityTicketRepo $userActivityTicketRepo, ActivityTicketService $userActivityTicketService, UserActivityTicketFormatter $userActivityTicketFormatter){
        DB::beginTransaction();
        $result = [
            'success' => false,
            'is_used_before' => null
        ];
        if(!$ticket_hash_id){
            return abort(404);
        };
        $ticket = $userActivityTicketRepo->findBy($ticket_hash_id,'ticket_id');
        if(!$ticket){
            return view('TicketUseResult', compact('result'));
        }
        $ticket['is_available'] = $userActivityTicketService->helper_ticket_is_available($ticket);
        /*檢查是否已使用過*/
        if(!empty($ticket['used_at'])) $result['is_used_before'] = $ticket['used_at'];
        /*更改票券為已使用狀態*/
        if(!$update_ticket = UserActivityTicket::where('id', $ticket->id)->update(['used_at' => date('Y-m-d H:i:s')])){
            return view('TicketUseResult', compact('result'));
        };
        /*send noti to merchant*/
        if($ticket['is_available']['status'] == 'available' && empty($result['is_used_before'])){
            if(!empty($product_merchant_telegram_acs = $ticket->Trip_activity_ticket->Trip_activity->Merchant->telegram_ac)){
                foreach($product_merchant_telegram_acs as $product_merchant_telegram_ac) {
                    $ticket_info = '🎫' . $ticket['sub_title'] . chr(10) . '💵' . $ticket['amount'] . chr(10) . '👨‍💼' . $ticket->owner['name'] . chr(10) . '產品名：' . $ticket['name'].chr(10).'編號'.'PN_180232'.$ticket['id'];
                    try {
                        $telegramService->message_sender($ticket_info, $product_merchant_telegram_ac['account_id']);
                    } catch (Exception $e) {
                    }
                }
            }
        }
        DB::commit();
        /*輸出*/
        $ticket = $userActivityTicketFormatter->dataFormat($ticket);
        $result['success'] = true;
        return view('TicketUseResult',compact('ticket','result'));
    }
}