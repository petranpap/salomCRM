<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Same value redirect()->intended() would have used — captured here instead
        // so the brief branded loading screen can pass it through as where to land
        // once its animation finishes.
        $next = $request->session()->pull('url.intended', route('dashboard', absolute: false));

        return redirect()->route('login.loading', ['next' => $next]);
    }

    /**
     * A brief branded loading screen shown right after a successful login, before
     * landing wherever the user was actually headed (`next` — either the page they
     * were trying to reach before being asked to log in, or the dashboard).
     */
    public function loading(Request $request): View
    {
        $next = $request->query('next', route('dashboard', absolute: false));

        // `next` arrives as a query string, so it's directly visible/shareable —
        // unlike the session value it started from. Only ever follow a same-site
        // relative path: reject an absolute URL or a protocol-relative "//host" one,
        // which browsers treat as absolute too.
        if (! is_string($next) || ! str_starts_with($next, '/') || str_starts_with($next, '//')) {
            $next = route('dashboard', absolute: false);
        }

        return view('auth.login-loading', ['next' => $next]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
