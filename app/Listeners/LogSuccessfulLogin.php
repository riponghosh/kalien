<?php

namespace App\Listeners;

use App\Events\SomeEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
	public function handle(Login $event)
	{
		$last_login_time = date('Y-m-d H:i:s');
		$event->user->last_login_at = $last_login_time;
		$event->user->socket_token = sha1($event->user->id.'add_salt'.$event->user->email.$last_login_time);
		$event->user->save();
	}
}
