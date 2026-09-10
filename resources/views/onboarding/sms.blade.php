@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    @include('onboarding._progress')

    <div class="mb-6">
        <h1 class="font-display text-2xl font-normal text-ts-text">SMS Reminders</h1>
        <p class="text-xs text-ts-text-subtle mt-1">
            Pick your SMS provider and enter its account details — appointment reminders for
            customers who've opted into SMS go out through this. Skip this if you don't have a
            provider account yet; you can add it anytime in Settings.
        </p>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6" x-data="{ provider: '{{ old('sms_driver', $salon->sms_driver) }}' }">
        <form action="{{ route('onboarding.store', ['step' => 'sms']) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Provider</label>
                <select name="sms_driver" x-model="provider" class="input-field">
                    <option value="">Not configured (logged only, nothing sent)</option>
                    <option value="cyta">Cyta (Cyprus)</option>
                </select>
            </div>

            <div x-show="provider === 'cyta'" x-cloak class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Cyta Username</label>
                    <input type="text" name="sms_credentials[username]"
                           value="{{ old('sms_credentials.username', $salon->sms_credentials['username'] ?? '') }}"
                           class="input-field @error('sms_credentials.username') ring-2 ring-red-400 @enderror">
                    @error('sms_credentials.username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Cyta Secret Key</label>
                    <input type="password" name="sms_credentials[secret_key]"
                           value="{{ old('sms_credentials.secret_key', $salon->sms_credentials['secret_key'] ?? '') }}"
                           class="input-field @error('sms_credentials.secret_key') ring-2 ring-red-400 @enderror">
                    @error('sms_credentials.secret_key')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">Message Language</label>
                    @php $smsLang = old('sms_credentials.language', $salon->sms_credentials['language'] ?? 'en'); @endphp
                    <select name="sms_credentials[language]" class="input-field">
                        <option value="en" {{ $smsLang === 'en' ? 'selected' : '' }}>English</option>
                        <option value="el" {{ $smsLang === 'el' ? 'selected' : '' }}>Greek</option>
                    </select>
                </div>
                <p class="col-span-2 text-[11px] text-ts-text-subtle">
                    Once saved, you can send a test message anytime from Settings → SMS Reminders.
                </p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Continue
                </button>
                <a href="{{ route('onboarding.show', ['step' => 'hours']) }}" class="text-sm text-ts-text-subtle hover:text-ts-text">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
