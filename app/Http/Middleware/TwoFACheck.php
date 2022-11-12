<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TwoFACheck
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
         if(!session()->has('userID') OR !session()->has('twoFA') ){ 
            return redirect('login')->with('notice', 'Please login frist');
        }
        // check session key  
         if( session()->get('twoFA') == true ){ 
            return redirect('/')->with('notice', 'Two factor-authentication successfull.');
        }
        
        return $next($request);
    }
}
