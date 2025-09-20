<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EnsureProfileIsSet
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
        $user = Auth::user();

        if ($user && !$user->is_profile_set) {
            // もし「今のリクエストURLが setup 以外」なら強制リダイレクト
            if (!$request->is('mypage/profile/setup')) {
                return redirect()->route('profile.setup');
            }
        }

        return $next($request);
    }
}
