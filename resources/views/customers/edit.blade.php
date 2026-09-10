@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-espresso-subtle dark:text-espresso-muted mb-1">Customers</p>
        <h1 class="font-display text-3xl font-semibold text-espresso dark:text-cream">Edit {{ $customer->name }}</h1>
    </div>

    <div class="card">
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="input-field @error('name') ring-2 ring-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="input-field @error('phone') ring-2 ring-red-400 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest text-espresso-muted mb-1.5">Notes</label>
                <textarea name="notes" rows="3" class="input-field">{{ old('notes', $customer->notes) }}</textarea>
            </div>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm text-espresso-muted cursor-pointer">
                    <input type="hidden" name="consent_sms" value="0">
                    <input type="checkbox" name="consent_sms" value="1" {{ old('consent_sms', $customer->consent_sms) ? 'checked' : '' }} class="rounded border-border-warm text-rose-sand focus:ring-rose-sand">
                    SMS reminders consent
                </label>
                <label class="flex items-center gap-2 text-sm text-espresso-muted cursor-pointer">
                    <input type="hidden" name="consent_email" value="0">
                    <input type="checkbox" name="consent_email" value="1" {{ old('consent_email', $customer->consent_email) ? 'checked' : '' }} class="rounded border-border-warm text-rose-sand focus:ring-rose-sand">
                    Email reminders consent
                </label>
            </div>
            <p class="text-xs text-espresso-subtle -mt-3">SMS reminders need a phone number above.</p>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update Customer</button>
                <a href="{{ route('customers.show', $customer) }}" class="btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
