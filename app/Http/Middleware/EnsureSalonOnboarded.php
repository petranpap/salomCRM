<?php

namespace App\Http\Middleware;

use App\Http\Controllers\OnboardingController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSalonOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Only the owner sets up salon-wide details — staff shouldn't be blocked by this.
        // Must also leave profile.* alone: EnsureMustChangePassword sends everything there
        // except profile.* itself while a password change is pending, and without this
        // exclusion the two middlewares bounce a new owner (who typically has both a
        // pending password change and an unfinished salon setup at once) back and forth
        // between /profile and /onboarding forever.
        if ($user && $user->isOwner() && $user->salon?->needsOnboarding()
            && ! $request->routeIs('onboarding.*') && ! $request->routeIs('profile.*') && ! $request->routeIs('logout')) {
            return redirect()->route('onboarding.show', ['step' => OnboardingController::STEPS[0]]);
        }

        return $next($request);
    }
}
