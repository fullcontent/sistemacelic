<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use User;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        

        if (Auth::guard($guard)->check()) {
            
            if(Auth::user()->privileges == 'admin')
            {   
                return redirect('/admin/home');
            }
            return redirect('/cliente/home');


        }

        return $next($request);
    }
}
