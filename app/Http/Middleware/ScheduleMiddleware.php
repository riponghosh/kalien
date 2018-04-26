<?php

namespace App\Http\Middleware;

use Closure;
use App\Schedule;
use Illuminate\Support\Facades\Auth;

class ScheduleMiddleware{

	public function __construct(){

	}
	public function handle($request, Closure $next){
		$user_id = Auth::user()->id;
		$AuthQuery = Schedule::where('guide_id',$user_id)->orwhere('tourist_id',$user_id)->where('status','enable')->first(); 
	    if(!$AuthQuery) return redirect('/');

	    return $next($request);
	}
}
?>