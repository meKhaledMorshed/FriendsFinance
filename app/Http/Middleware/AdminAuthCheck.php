<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuthCheck
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
        if (!session()->has('userID') or !session()->has('login') or (session()->get('login') == false)) {
            return redirect()->route('login')->with('notice', 'Please login frist.');
        }
        // check admin session key 
        if (!session()->has('adminLogin') or (session()->get('adminLogin') == false)) {
            return redirect()->route('user.home')->with('notice', 'You are not authorized.');
        }
        // check twoFA session key 
        if (!session()->has('twoFA') or (session()->get('twoFA') == false)) {
            return redirect()->route('login.checkpost')->with('notice', 'You are not authorized.');
        }

        return $next($request);
    }
}
