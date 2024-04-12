<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $roleIds = ['admin' => 1, 'user' => 0];

        $allowedRoleIds = [];
        foreach ($roles as $role)
        {
            if(isset($roleIds[$role]))
            {
                $allowedRoleIds[] = $roleIds[$role];
            }
        }
        $allowedRoleIds = array_unique($allowedRoleIds);

        if(Auth::check()) {
            if(in_array(Auth::user()->role, $allowedRoleIds)) {
                return $next($request);
            }
        }
        return redirect('/unauthorized');
        
    }
}
