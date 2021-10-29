<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JefeDeptoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()){
            if (auth()->user()->id_puesto == 2 || auth()->user()->id_puesto == 8)
                return $next($request);
        }

        return redirect()->back();
    }
}
