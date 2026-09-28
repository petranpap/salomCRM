<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $wasForcedChange = $request->user()->must_change_password;

        $request->user()->update([
            'password'              => Hash::make($validated['password']),
            'must_change_password'  => false,
        ]);

        // A forced first-login change isn't gated by the onboarding middleware (only
        // /dashboard and friends are), so without this the user is just left sitting
        // on /profile with nothing routing them onward to onboarding.
        if ($wasForcedChange) {
            return redirect()->route('dashboard')->with('status', 'password-updated');
        }

        return back()->with('status', 'password-updated');
    }
}
