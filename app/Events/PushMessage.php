<?php

namespace App\Events;

use Illuminate\Support\Facades\DB;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PushMessage implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $message;
    public $send_to_user_token; 
    public $room_id;//chatroom id

    public function __construct($send_to_user_id,$message,$room_id)
    {
        $this->message = $message;
        $token = DB::table('users')->select('socket_token')
                                                      ->where('id',$send_to_user_id)
                                                      ->get();
        $token = $token[0]->socket_token;
        $token = sha1($token.'add_salt');
        $this->send_to_user_token = $token;
        $this->room_id = $room_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['chatRoomMessage'];
    }
}
