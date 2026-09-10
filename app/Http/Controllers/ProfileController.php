<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\PendingChange;
use App\Models\StaffProfile;
use App\Support\WeeklySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $staffProfile = $user->staffProfile;
        $isApprover = $user->isOwner() || $user->isSuperAdmin();

        $pendingWorkingHoursChange = $staffProfile
            ? PendingChange::where('subject_type', StaffProfile::class)
                ->where('subject_id', $staffProfile->id)
                ->where('status', 'pending')
                ->latest()
                ->first()
            : null;

        return view('profile.edit', [
            'user'                      => $user,
            'staffProfile'              => $staffProfile,
            'isApprover'                => $isApprover,
            'pendingWorkingHoursChange' => $pendingWorkingHoursChange,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update (or request approval for) the current user's own working hours.
     */
    public function updateWorkingHours(Request $request): RedirectResponse
    {
        $user = $request->user();
        $staffProfile = $user->staffProfile;

        abort_unless($staffProfile, 404);

        $request->validate(['working_hours' => ['nullable', 'array']]);
        $workingHours = WeeklySchedule::fromRequest($request->input('working_hours', []));

        if ($salonHours = $user->salon?->normalizedOpeningHours()) {
            $errors = WeeklySchedule::validateWithin($workingHours, $salonHours, $user->name);

            if ($errors) {
                throw ValidationException::withMessages(['working_hours' => $errors]);
            }
        }

        if ($user->isOwner() || $user->isSuperAdmin()) {
            $staffProfile->update(['working_hours' => $workingHours]);

            return Redirect::route('profile.edit')->with('success', 'Working hours updated.');
        }

        PendingChange::create([
            'salon_id'     => $user->salon_id,
            'subject_type' => StaffProfile::class,
            'subject_id'   => $staffProfile->id,
            'action'       => 'update',
            'payload'      => ['working_hours' => $workingHours],
            'submitted_by' => $user->id,
        ]);

        return Redirect::route('profile.edit')->with('success', 'Working hours change submitted for owner approval.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
