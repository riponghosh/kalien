<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatRoomController;

class ChatroomMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected $chatroomController;

    public function __construct(ChatRoomController $chatroomController){
        $this->chatroomController = $chatroomController;
    }

    public function handle($request, Closure $next)
    {
        
        if( isset(Auth::user()->id) ){
            $user_id = Auth::user()->id;
            $chatroom_id = $request->room_id;
            if($this->chatroomController->room_auth_checkable($chatroom_id,$user_id) == false){
                return redirect('/');
            }else{
                //print_r($chatroom_id);
            };
        };
        
        return $next($request);
    }
}
