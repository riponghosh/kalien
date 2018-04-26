<?php

namespace App\Events;

use Illuminate\Support\Facades\DB;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ScheduleEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $schedule_id;
    public $send_to_user_token; 
    public $result;
    public $component;

    public function __construct($result,$user_id,$schedule_id, $component)
    {
        $this->schedule_id = $schedule_id;
        $this->result = $result;
        $this->component = $component;
        /*user*/
		$token = DB::table('users')->select('socket_token')
			->where('id',$user_id)
			->get();
		$token = $token[0]->socket_token;
		$token = sha1($token.'add_salt');
		$this->send_to_user_token = $token;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
    	if($this->component == 'event_block'){
			return ['scheduleEventBlockEvent'];
		}elseif($this->component == 'schedule'){
			return ['scheduleEvent'];
		}

    }
}
