@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-espresso-subtle dark:text-espresso-muted mb-1">Customers</p>
        <h1 class="font-display text-3xl font-semibold text-espresso dark:text-cream">New Customer</h1>
    </div>

    <div class="card">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-field @error('name') ring-2 ring-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="input-field @error('phone') ring-2 ring-red-400 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input-field @error('email') ring-2 ring-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="section-label block mb-1.5">Date of Birth</label>
                    <div class="relative">
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="input-field pr-10">
                        <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Notes</label>
                <textarea name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm text-espresso-muted cursor-pointer">
                    <input type="hidden" name="consent_sms" value="0">
                    <input type="checkbox" name="consent_sms" value="1" {{ old('consent_sms') ? 'checked' : '' }} class="rounded border-border-warm text-rose-sand focus:ring-rose-sand">
                    SMS reminders consent
                </label>
                <label class="flex items-center gap-2 text-sm text-espresso-muted cursor-pointer">
                    <input type="hidden" name="consent_email" value="0">
                    <input type="checkbox" name="consent_email" value="1" {{ old('consent_email') ? 'checked' : '' }} class="rounded border-border-warm text-rose-sand focus:ring-rose-sand">
                    Email reminders consent
                </label>
            </div>
            <p class="text-xs text-espresso-subtle -mt-3">SMS reminders need a phone number above.</p>
            @if($salons->isNotEmpty())
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Assign to Salon *</label>
                <select name="salon_id" class="input-field @error('salon_id') ring-2 ring-red-400 @enderror" required>
                    <option value="">Select salon…</option>
                    @foreach($salons as $salon)
                        <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>
                            {{ $salon->name }}
                        </option>
                    @endforeach
                </select>
                @error('salon_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Save Customer</button>
                <a href="{{ route('customers.index') }}" class="btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
