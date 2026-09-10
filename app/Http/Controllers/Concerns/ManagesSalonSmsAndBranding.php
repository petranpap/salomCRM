<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Shared by SalonSettingsController (Settings → SMS Reminders / Receipt Branding) and
 * OnboardingController (the same two sections as wizard steps) so the validation rules
 * and save behavior — including safe logo replacement — live in exactly one place.
 */
trait ManagesSalonSmsAndBranding
{
    protected function applySmsSettings(Request $request, Salon $salon): void
    {
        $data = $request->validate([
            // Add a new provider here (plus a matching case in SmsManager::resolveInner())
            // as more gateways come online.
            'sms_driver'                  => ['nullable', 'in:cyta'],
            'sms_credentials.username'    => ['nullable', 'string', 'max:255', 'required_if:sms_driver,cyta'],
            'sms_credentials.secret_key'  => ['nullable', 'string', 'max:255', 'required_if:sms_driver,cyta'],
            'sms_credentials.language'    => ['nullable', 'in:en,el'],
        ], [
            'sms_credentials.username.required_if'   => 'Enter the Cyta account username.',
            'sms_credentials.secret_key.required_if' => 'Enter the Cyta secret key.',
        ]);

        // Drop any leftover credentials for a provider that isn't the selected one —
        // a salon switching gateways shouldn't leave a stale secret sitting in storage.
        $data['sms_credentials'] = $data['sms_driver'] === 'cyta'
            ? ($data['sms_credentials'] ?? [])
            : null;

        $salon->update($data);
    }

    protected function applyBrandingSettings(Request $request, Salon $salon): void
    {
        $data = $request->validate([
            'logo'                        => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'remove_logo'                 => ['nullable', 'boolean'],
            'receipt_primary_color'       => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'receipt_secondary_color'     => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'receipt_template'            => ['required', 'in:' . implode(',', Salon::RECEIPT_TEMPLATES)],
        ], [
            'receipt_primary_color.regex'   => 'Enter a valid color, e.g. #7d523c.',
            'receipt_secondary_color.regex' => 'Enter a valid color, e.g. #2f2a25.',
        ]);

        $oldLogoPath = $salon->logo_path;

        if ($request->boolean('remove_logo')) {
            $data['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('salon-logos', 'public');
        }

        unset($data['logo'], $data['remove_logo']);

        $salon->update($data);

        // Only delete the old file once the new state is safely saved, and only when it
        // actually changed — never touch storage on a validation failure or a no-op save.
        if ($oldLogoPath && $oldLogoPath !== $salon->logo_path) {
            Storage::disk('public')->delete($oldLogoPath);
        }
    }
}
