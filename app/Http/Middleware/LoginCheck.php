<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginCheck
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
        // check DB empty or not  
        $users = DB::table('users')->first();
        if (!isset($users->id)) {
            Session()->flush();
            return redirect('addentity')->with('notice', "Hello Admin! Please input your entity's information below ⬇️");
        }
        // check session key 
        if (session()->get('login') == true) {
            return redirect('/');
        }

        return $next($request);
    }
}
