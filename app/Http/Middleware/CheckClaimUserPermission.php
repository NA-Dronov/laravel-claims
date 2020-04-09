<?php

namespace App\Http\Middleware;

use Closure;

class CheckClaimUserPermission
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
        $claim = $request->route()->parameter('claim');

        $isCurrentUser = auth()->user()->user_id == $claim->user_id;
        $isManager = auth()->user()->hasRole("manager");
        if (!$isCurrentUser && !$isManager) {
            return back()->withErrors(['msg' => 'У вас недостаточно прав']);
        }

        return $next($request);
    }
}
