<?php

namespace App\Events;
use App\User;
use App\UserNotification;

use Illuminate\Support\Facades\DB;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PushUserNotification implements ShouldBroadcast
{
	use InteractsWithSockets, SerializesModels;

	/**
	 * The notification id.
	 *
	 * @var string
	 */
	protected $id = null;
	/**
	 * The notification title.
	 *
	 * @var string
	 */
	protected $user_id;
	public $title;
	public $body;
	public $icon = null;
	public $is_read = 0;
	public $actions = [];

	public $send_to_user_token;
	/*
	public function __construct($user_id){
		$this->user_id = $user_id;
		$token = User::where('id',$this->user_id)->first();
		$this->send_to_user_token = sha1($token->socket_token.'add_salt');
	}
	*/
	public function __construct($body = ''){
		$this->title = '';
		$this->body = $body;
	}

	public function user_id($value){
		$this->user_id = $value;
		return $this;
	}
	public function title($value){
		$this->title = $value;
		return $this;
	}
	public function body($value){
		$this->body = $value;
		return $this;
	}
	public function icon($value){
		$this->icon = $value;
		return $this;
	}
	public static function create($body = ''){
		return new static($body);
	}
	public function save(){
		$user_notification = new UserNotification;
		$user_notification->title = $this->title;
		$user_notification->body = $this->body;
		$user_notification->icon = $this->icon;
		$user_notification->user_id = $this->user_id;
		$user_notification->save();

		return $this;
	}
	public function send(){
		$token = User::where('id',$this->user_id)->first();
		$this->send_to_user_token = sha1($token->socket_token.'add_salt');

		return $this;
	}
	public function broadcastOn()
	{
		return ['UserNotification'];
	}

}
?>

