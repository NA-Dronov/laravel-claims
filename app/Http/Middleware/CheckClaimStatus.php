<?php

namespace App\Http\Middleware;

use App\Models\ClaimStatus;
use Closure;

class CheckClaimStatus
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

        if ($claim->status == ClaimStatus::CLOSED) {
            return back()->withErrors(['msg' => 'Заявка закрыта']);
        }

        return $next($request);
    }
}
