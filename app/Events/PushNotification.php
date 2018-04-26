<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\User;

class PushNotification implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $token;
    /**
     * @var string
     */
    public $message;
    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param      $message
     */
    public function __construct(User $user, $message)
    {
        $this->token = sha1($user->id . '|' . $user->email);
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['notification'];
        /*return new PrivateChannel('notification');*/
    }
}
