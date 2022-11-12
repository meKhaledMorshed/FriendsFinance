<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminController;
use Closure;
use Illuminate\Http\Request;

class MasterAdminCheck
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
        $admin = new AdminController();
        $admin = $admin->admin;

        $role = ['master', 'super'];

        if (!in_array($admin->role, $role) || $admin->editPermit != 1) {
            return redirect()->route('admin.user')->with('notice', $admin->editPermit . 'Sorry !! You have no permission.');
        }
        return $next($request);
    }
}
