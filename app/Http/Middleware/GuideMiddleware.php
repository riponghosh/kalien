<?php

namespace App\Http\Middleware;

use Closure;
use App\Guide;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;

class GuideMiddleware{

	public function __construct(){

	}
	public function handle($request, Closure $next){
		$query = UserProfileController::auth_user_is_tourGuide();
	    if(!$query) return redirect('/');

	    return $next($request);
	}
}

?>