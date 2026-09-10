<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesSalonSmsAndBranding;
use App\Models\Salon;
use App\Support\WeeklySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * First-login guided setup for a new salon's owner — password change is handled
 * separately by EnsureMustChangePassword and always resolves first; this wizard
 * only covers the salon-wide details an owner would otherwise have to go find
 * across several Settings sections. Everything set here stays fully editable in
 * Settings afterward — this is a one-time nudge, not a lockout.
 */
class OnboardingController extends Controller
{
    use ManagesSalonSmsAndBranding;

    const STEPS = ['details', 'vat', 'hours', 'sms', 'branding'];

    public function show(string $step): View
    {
        abort_unless(in_array($step, self::STEPS, true), 404);

        $salon = $this->salon();

        return view("onboarding.{$step}", [
            'salon'      => $salon,
            'stepIndex'  => array_search($step, self::STEPS) + 1,
            'totalSteps' => count(self::STEPS),
        ]);
    }

    public function store(Request $request, string $step): RedirectResponse
    {
        abort_unless(in_array($step, self::STEPS, true), 404);

        $salon = $this->salon();

        match ($step) {
            'details'  => $this->saveDetails($request, $salon),
            'vat'      => $this->saveVat($request, $salon),
            'hours'    => $this->saveHours($request, $salon),
            'sms'      => $this->applySmsSettings($request, $salon),
            'branding' => $this->applyBrandingSettings($request, $salon),
        };

        $nextIndex = array_search($step, self::STEPS) + 1;

        if ($nextIndex >= count(self::STEPS)) {
            $salon->update(['onboarding_completed_at' => now()]);

            return redirect()->route('dashboard')
                ->with('success', "You're all set! You can change any of this anytime in Settings.");
        }

        return redirect()->route('onboarding.show', ['step' => self::STEPS[$nextIndex]]);
    }

    public function skip(): RedirectResponse
    {
        $this->salon()->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Setup skipped — you can configure your salon anytime in Settings.');
    }

    protected function saveDetails(Request $request, Salon $salon): void
    {
        $salon->update($request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'email'   => ['nullable', 'email', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ]));
    }

    protected function saveVat(Request $request, Salon $salon): void
    {
        $salon->update($request->validate([
            'vat_number' => ['nullable', 'string', 'max:60'],
            'vat_rate'   => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]));
    }

    protected function saveHours(Request $request, Salon $salon): void
    {
        $request->validate(['opening_hours' => ['nullable', 'array']]);

        $salon->update(['opening_hours' => WeeklySchedule::fromRequest($request->input('opening_hours', []))]);
    }

    protected function salon(): Salon
    {
        $user = auth()->user();

        abort_unless($user->isOwner() && $user->salon, 403);

        return $user->salon;
    }
}
