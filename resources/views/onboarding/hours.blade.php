@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    @include('onboarding._progress')

    <div class="mb-6">
        <h1 class="font-display text-2xl font-normal text-ts-text">When are you open?</h1>
        <p class="text-xs text-ts-text-subtle mt-1">
            Staff working hours and appointment booking are both checked against this —
            uncheck a day to mark the salon closed on it.
        </p>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-6">
        <form action="{{ route('onboarding.store', ['step' => 'hours']) }}" method="POST" class="space-y-5">
            @csrf

            <x-weekly-schedule field-name="opening_hours" label=""
                :hours="$salon->normalizedOpeningHours()" />

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Continue
                </button>
                <a href="{{ route('onboarding.show', ['step' => 'vat']) }}" class="text-sm text-ts-text-subtle hover:text-ts-text">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
