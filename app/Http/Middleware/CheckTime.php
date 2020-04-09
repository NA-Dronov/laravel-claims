<?php

namespace App\Http\Middleware;

use App\Models\Claim;
use Carbon\Carbon;
use Closure;

class CheckTime
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
        $last_claim = Claim::where('user_id', auth()->user()->user_id)->orderBy('created_at', 'desc')->first();
        $new_claim_date = $last_claim->created_at->copy()->addDay();
        $now = Carbon::now();

        $diff = $new_claim_date->floatDiffInDays($now, false);

        if ($diff < 0) {
            return redirect()->route('claims.index')
                ->withErrors(['msg' => "Отправка нового заявления возможна через {$new_claim_date->diffInHours($now)}:{$new_claim_date->diff($now)->format('%I:%S')}"]);
        }

        return $next($request);
    }
}
