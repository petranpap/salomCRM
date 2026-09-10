<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Lets an existing super_admin create another one — previously only possible from the
 * console (`make:superadmin`), which meant a platform operator without shell access
 * couldn't be onboarded at all. Route-gated to super_admin only (see routes/web.php).
 */
class PlatformAdminController extends Controller
{
    public function index(): View
    {
        $admins = User::where('role', 'super_admin')->orderBy('name')->get();

        return view('platform-admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('platform-admins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'                  => $validated['name'],
            'email'                 => $validated['email'],
            'password'              => Hash::make($validated['password']),
            'role'                  => 'super_admin',
            'salon_id'              => null,
            // Same reasoning as staff accounts: the creator picks this password, not
            // the new admin, so force them to set their own on first login.
            'must_change_password'  => true,
        ]);

        return redirect()->route('platform-admins.index')->with('success', 'Super admin created.');
    }

    /**
     * A locked-out super_admin can't unlock themselves — they can't log in to click the
     * button. Another super_admin does it for them (or they wait out the lockout).
     */
    public function unlock(User $admin): RedirectResponse
    {
        abort_unless($admin->isSuperAdmin(), 404);

        $admin->unlock();

        return redirect()->route('platform-admins.index')->with('success', "{$admin->name}'s account has been unlocked.");
    }
}
