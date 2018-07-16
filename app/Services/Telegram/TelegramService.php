<?php

namespace App\Services\Telegram;

use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    function __construct()
    {
    }

    function message_sender($message, $send_to_id, $parse_node = 'HTML'){
        return Telegram::sendMessage([
            'chat_id' => $send_to_id,
            'parse_mode' => $parse_node,
            'text' => $message
        ]);
    }
}
