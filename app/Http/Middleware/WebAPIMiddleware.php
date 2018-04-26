<?php

namespace App\Http\Middleware;

use App\Exceptions\Auth\UserUnAuthException;
use App\Exceptions\JsonResponseException;
use Closure;
use Illuminate\Support\Facades\Auth;

class WebAPIMiddleware
{
    public function handle($request, Closure $next)
    {
        if(!Auth::check()){
            return response()->json(['success' => 'false', 'msg' => '401'], 401);
        }
        return $next($request);
    }
}
?>