<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;

class TouristMiddleware{

	public function __construct(){

	}
	public function handle($request, Closure $next){
		$query = UserProfileController::auth_user_is_tourist();
		if($request->ajax() && !$query) return response(array('success' => false, 'msg' => '基本資料不足。', 'status'=>'not_tourist'));
	    if(!$query) return redirect('/');

	    return $next($request);
	}
}

?>