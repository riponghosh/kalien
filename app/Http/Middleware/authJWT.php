<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use League\Flysystem\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class authJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            Config::set('auth.providers.users.model', \App\Merchant\Merchant::class);
            // 如果用户登陆后的所有请求没有jwt的token抛出异常
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['success'=>'false','msg' => 'Token invalid', 'code' => '001'], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['success'=>'false','msg' => 'Token expired', 'code' => '002'], 401);
            }else{
                return response()->json(['success'=>'false','msg' => 'Something wrong', 'code' => '003'], 500);
            }
        }

        return $next($request);
    }
}
