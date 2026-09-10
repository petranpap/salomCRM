<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Salon;
use App\Models\StaffProfile;
use App\Models\Treatment;
use App\Models\User;
use App\Support\WeeklySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function index()
    {
        $query = User::with('staffProfile', 'salon')
            ->where('role', '!=', 'super_admin')
            ->orderBy('name');

        if (!Auth::user()->isSuperAdmin()) {
            $query->where('salon_id', Auth::user()->salon_id);
        }

        $staff = $query->paginate(20);
        $salons = Auth::user()->isSuperAdmin() ? \App\Models\Salon::orderBy('name')->get() : collect();

        return view('staff.index', compact('staff', 'salons'));
    }

    public function create()
    {
        $salons = Auth::user()->isSuperAdmin() ? \App\Models\Salon::orderBy('name')->get() : collect();
        $salonHours = Auth::user()->salon?->normalizedOpeningHours();

        return view('staff.create', compact('salons', 'salonHours'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users'],
            'role'          => ['required', 'in:owner,staff'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'job_title'     => ['nullable', 'string', 'max:100'],
            'specialty'     => ['nullable', 'string', 'max:100'],
            'color'         => ['nullable', 'string', 'size:7'],
            'salon_id'      => ['nullable', 'exists:salons,id'],
            'working_hours' => ['nullable', 'array'],
        ]);

        $salonId = Auth::user()->isSuperAdmin()
            ? ($validated['salon_id'] ?? null)
            : Auth::user()->salon_id;

        $workingHours = WeeklySchedule::fromRequest($request->input('working_hours', []));
        $this->assertWithinSalonHours($workingHours, $salonId, $validated['name']);

        $user = User::create([
            'name'                  => $validated['name'],
            'email'                 => $validated['email'],
            'role'                  => $validated['role'],
            'password'              => Hash::make($validated['password']),
            'salon_id'              => $salonId,
            // The admin picks this temporary password, not the staff member — force
            // them to set their own on first login rather than trusting they'll change it.
            'must_change_password'  => true,
        ]);

        StaffProfile::create([
            'user_id'       => $user->id,
            'job_title'     => $validated['job_title'] ?? null,
            'specialty'     => $validated['specialty'] ?? null,
            'color'         => $validated['color'] ?? null,
            'working_hours' => $workingHours,
            'is_active'     => true,
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit(User $staff)
    {
        $this->authorizeStaffAccess($staff);

        $staff->load('staffProfile');
        $salons = Auth::user()->isSuperAdmin() ? \App\Models\Salon::orderBy('name')->get() : collect();
        $salonHours = $staff->salon?->normalizedOpeningHours();

        return view('staff.edit', compact('staff', 'salons', 'salonHours'));
    }

    public function update(Request $request, User $staff)
    {
        $this->authorizeStaffAccess($staff);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email,' . $staff->id],
            'role'          => ['required', 'in:owner,staff'],
            'job_title'     => ['nullable', 'string', 'max:100'],
            'specialty'     => ['nullable', 'string', 'max:100'],
            'color'         => ['nullable', 'string', 'size:7'],
            'is_active'     => ['boolean'],
            'password'      => ['nullable', 'confirmed', Rules\Password::defaults()],
            'salon_id'      => ['nullable', 'exists:salons,id'],
            'working_hours' => ['nullable', 'array'],
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ];

        if (Auth::user()->isSuperAdmin() && array_key_exists('salon_id', $validated)) {
            $updateData['salon_id'] = $validated['salon_id'];
        }

        $effectiveSalonId = $updateData['salon_id'] ?? $staff->salon_id;
        $workingHours = WeeklySchedule::fromRequest($request->input('working_hours', []));
        $this->assertWithinSalonHours($workingHours, $effectiveSalonId, $validated['name']);

        $staff->update($updateData);

        if (!empty($validated['password'])) {
            // Same reasoning as account creation: the admin is choosing this password,
            // not the staff member, so force them to set their own on next login.
            $staff->update([
                'password'              => Hash::make($validated['password']),
                'must_change_password'  => true,
            ]);
        }

        $staff->staffProfile()->updateOrCreate(
            ['user_id' => $staff->id],
            [
                'job_title'     => $validated['job_title'] ?? null,
                'specialty'     => $validated['specialty'] ?? null,
                'color'         => $validated['color'] ?? null,
                'working_hours' => $workingHours,
                'is_active'     => $request->boolean('is_active'),
            ]
        );

        return redirect()->route('staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(User $staff)
    {
        $this->authorizeStaffAccess($staff);

        // appointments.staff_id and treatments.staff_id both cascade-delete from
        // staff_profiles, which itself cascades from users — so deleting this account
        // would silently wipe every appointment and treatment record this person was
        // ever involved in, for every customer, with no way back. Deactivating (the
        // "Active" toggle) is the safe way to remove someone who has real history;
        // hard delete is only for a staff member who never actually did anything.
        $profileId = $staff->staffProfile?->id;

        $hasHistory = $profileId && (
            Appointment::where('staff_id', $profileId)->exists()
            || Treatment::where('staff_id', $profileId)->exists()
        );

        if ($hasHistory) {
            return redirect()->route('staff.edit', $staff)->with(
                'error',
                "{$staff->name} has appointment or treatment history and can't be deleted — use the Active toggle to remove them from scheduling instead."
            );
        }

        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member removed.');
    }

    /**
     * A staff member can't be scheduled outside the salon's own opening hours.
     */
    protected function assertWithinSalonHours(array $workingHours, ?int $salonId, string $staffName): void
    {
        $salon = $salonId ? Salon::find($salonId) : null;

        if (! $salon) {
            return;
        }

        $errors = WeeklySchedule::validateWithin($workingHours, $salon->normalizedOpeningHours(), $staffName);

        if ($errors) {
            throw ValidationException::withMessages(['working_hours' => $errors]);
        }
    }

    /**
     * Staff accounts are tenant-scoped: an owner may only manage staff within their own
     * salon. 404 (not 403) so a cross-salon ID doesn't even confirm the record exists.
     */
    protected function authorizeStaffAccess(User $staff): void
    {
        abort_if($staff->isSuperAdmin(), 404);

        $currentUser = Auth::user();

        abort_if(
            ! $currentUser->isSuperAdmin() && $staff->salon_id !== $currentUser->salon_id,
            404
        );
    }

    /**
     * Lets the account back in immediately instead of waiting out the lockout window
     * (User::MAX_FAILED_LOGIN_ATTEMPTS wrong passwords locks it for
     * User::LOCKOUT_MINUTES). Deliberately super_admin-only, not owner — routed under
     * the super_admin middleware group, not the staff resource's owner group.
     */
    public function unlock(User $staff)
    {
        abort_if($staff->isSuperAdmin(), 404);

        $staff->unlock();

        return redirect()->route('staff.edit', $staff)->with('success', "{$staff->name}'s account has been unlocked.");
    }
}
