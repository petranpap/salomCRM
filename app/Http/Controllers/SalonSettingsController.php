<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesSalonSmsAndBranding;
use App\Models\User;
use App\Services\Sms\SmsManager;
use App\Support\WeeklySchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class SalonSettingsController extends Controller
{
    use ManagesSalonSmsAndBranding;

    public function edit(): View
    {
        $salon = $this->authorizedSalon();
        $canEdit = $this->canEdit();

        return view('settings.salon', compact('salon', 'canEdit'));
    }

    public function update(Request $request): RedirectResponse
    {
        $salon = $this->authorizedSalon();

        abort_unless($this->canEdit(), 403);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:120'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:120'],
            'address'       => ['nullable', 'string', 'max:255'],
            'vat_number'    => ['nullable', 'string', 'max:60'],
            'vat_rate'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'opening_hours' => ['nullable', 'array'],
        ]);

        $data['opening_hours'] = WeeklySchedule::fromRequest($request->input('opening_hours', []));

        $salon->update($data);

        $this->applySmsSettings($request, $salon);
        $this->applyBrandingSettings($request, $salon);

        return redirect()->route('salon-settings.edit')->with('success', 'Salon settings updated.');
    }

    /**
     * Send a real SMS through the salon's currently *saved* provider/credentials, so an
     * owner can confirm the account actually works before relying on it for reminders.
     */
    public function sendTestSms(Request $request, SmsManager $smsManager): RedirectResponse
    {
        $salon = $this->authorizedSalon();

        abort_unless($this->canEdit(), 403);

        $data = $request->validate([
            'test_phone' => ['required', 'string', 'max:30'],
        ]);

        if (! $salon->sms_driver) {
            return redirect()->route('salon-settings.edit')
                ->with('error', 'Pick and save an SMS provider before sending a test.');
        }

        try {
            $smsManager->send(
                $salon,
                $data['test_phone'],
                "Test message from {$salon->name} — SMS reminders are working."
            );
        } catch (Throwable $e) {
            return redirect()->route('salon-settings.edit')
                ->with('error', 'Test SMS failed: ' . $e->getMessage());
        }

        return redirect()->route('salon-settings.edit')
            ->with('success', "Test SMS sent to {$data['test_phone']}.");
    }

    protected function authorizedSalon()
    {
        $user = auth()->user();

        abort_unless($user->hasRole(...User::SALON_ROLES) && $user->salon, 403);

        return $user->salon;
    }

    protected function canEdit(): bool
    {
        $user = auth()->user();

        return $user->isOwner() || $user->isSuperAdmin();
    }
}
