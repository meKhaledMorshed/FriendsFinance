<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // check session key 
         if(!session()->has('userID') OR !session()->has('login') OR ( session()->get('login') == false ) ){ 
    
            return redirect('login')->with('notice', 'Please login frist');
        }
        // check twoFA session key 
         if( !session()->has('twoFA') OR ( session()->get('twoFA') == false ) ){
            return redirect('checkpost')->with('notice', 'You are not authorized.');
        }

        return $next($request);
    }
}
