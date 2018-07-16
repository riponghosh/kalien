<?php

namespace App\Http\Controllers;

use App\Models\TelegramChatroom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Flysystem\Exception;
use Telegram\Bot\Laravel\Facades\Telegram;


class TelegramBotController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function updatedActivity()
    {
        $activity = Telegram::getUpdates();
        dd($activity);
    }

    public function setWebHook(){
        //Telegram::removeWebhook();
        if($res = Telegram::setWebHook(['url' => env('APP_URL').'/telegram_webhook/'.env('TELEGRAM_WEB_HOOK_TOKEN')])){
            return 'success';
        }else{
            return 'failed';
        };
    }

    public function getWebhookUpdates(TelegramChatroom $telegramChatroom){
        try {
            $chat_data = Telegram::getWebHookUpdates();
            $telegramChatroom->create($this->message_compiler($chat_data));
            Telegram::forwardMessage([
                'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
                'from_chat_id' => $chat_data['message']['chat']['id'],
                'message_id' => $chat_data['message']['message_id']
            ]);

        }catch (Exception $e){

        }
        /*
        if(count($res) > 0){
            $insert_data = collect($res)->map(function($chat_data){
                Telegram::forwardMessage([
                    'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
                    'from_chat_id' => $chat_data['message']['chat']['id'],
                    'message_id' => $chat_data['message']['message_id']
                ]);
                return $this->message_compiler($chat_data);
            });
            $telegramChatroom->insert($insert_data);
        }
        */

        return;

    }

    public function message_compiler($telegram_data){
        $message = $telegram_data['message'];
        return [
            't_message_id' => $message['message_id'],
            'sent_at' => Carbon::createFromTimestamp($message['date'])->toDateTimeString(),
            'content' => isset($message['text']) ? $message['text'] : '',
            'sender_name' => optional($message['chat'])['first_name'],
            'sender_id' => optional($message['chat'])['id'],
            'raw_data' => json_encode($telegram_data,true),
        ];
    }
    public function storeMessage(Request $request)
    {
        $text = "";
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', ''),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        return redirect()->back();
    }

}
