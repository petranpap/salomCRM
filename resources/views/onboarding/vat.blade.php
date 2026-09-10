@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    @include('onboarding._progress')

    <div class="mb-6">
        <h1 class="font-display text-2xl font-normal text-ts-text">Are you VAT-registered?</h1>
        <p class="text-xs text-ts-text-subtle mt-1">
            Leave this blank if you're not — receipts will simply show a total with no VAT
            breakdown until you add this. Add it now, or anytime later in Settings.
        </p>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('onboarding.store', ['step' => 'vat']) }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">VAT Number</label>
                    <input type="text" name="vat_number" value="{{ old('vat_number', $salon->vat_number) }}"
                           placeholder="e.g. CY12345678X" class="input-field">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1.5">VAT Rate (%)</label>
                    <input type="number" name="vat_rate" value="{{ old('vat_rate', $salon->vat_rate) }}"
                           step="0.01" min="0" max="100" placeholder="e.g. 19"
                           class="input-field @error('vat_rate') ring-2 ring-red-400 @enderror">
                    @error('vat_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Continue
                </button>
                <a href="{{ route('onboarding.show', ['step' => 'details']) }}" class="text-sm text-ts-text-subtle hover:text-ts-text">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
