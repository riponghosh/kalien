<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Cookie;
use Closure;

class LocaleLanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        $lans = ['zh_tw','en', 'jp'];
        if(in_array($request->cookie('web_language'),$lans)){
            app()->setLocale($request->cookie('web_language'));
        }else{
            Cookie::queue('web_language', app()->getLocale());
        }

        return $next($request);
    }
}
?>
