<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfCannotAccessFilamentPanel
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $panel = filament()->getCurrentPanel();

        if ($user && !$user->canAccessPanel($panel)) {
            return redirect(env('APP_URL'));
        }

        return $next($request);
    }
}
