<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->must_change_password && ! $request->routeIs('profile.*')) {
            return redirect()->route('profile.edit')
                ->with('error', 'You must set a new password before continuing.');
        }

        return $next($request);
    }
}
