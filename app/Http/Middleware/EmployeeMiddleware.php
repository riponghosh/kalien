<?php

namespace App\Http\Middleware;

use Closure;
use App\Guide;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;

class EmployeeMiddleware{

    public function __construct(){

    }
    public function handle($request, Closure $next){
        $p_employees = json_decode(env('P_EMPLOYEES'));
        if(count($p_employees) > 0){
            if(!in_array(Auth::user()->id, $p_employees)) return redirect('/');
        }
        return $next($request);
    }

}
?>

