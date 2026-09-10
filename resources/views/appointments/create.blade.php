@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <p class="section-label">Appointments</p>
        <h1 class="page-title">New Appointment</h1>
    </div>
    <div class="card">
        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Super admin: salon indicator (read-only, derived from customer) --}}
            @if($isSuperAdmin && $salons->isNotEmpty())
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
                Creating as <strong>Super Admin</strong> — salon is assigned automatically from the selected customer.
            </div>
            @endif

            <div>
                <label class="section-label block mb-1.5">Customer *</label>
                <select name="customer_id" class="input-field @error('customer_id') ring-2 ring-red-400 @enderror">
                    <option value="">Select customer…</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                            @if($isSuperAdmin && $customer->salon) — {{ $customer->salon->name }} @endif
                        </option>
                    @endforeach
                </select>
                @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="section-label block mb-1.5">Service *</label>
                    <select name="service_id" id="appt-service"
                            class="input-field @error('service_id') ring-2 ring-red-400 @enderror">
                        <option value="">Select service…</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                    data-duration="{{ $service->duration_min }}"
                                    {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ $service->duration_min }}min)
                                @if($isSuperAdmin && $service->salon) — {{ $service->salon->name }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="section-label block mb-1.5">Staff *</label>
                    <select name="staff_id" class="input-field @error('staff_id') ring-2 ring-red-400 @enderror">
                        <option value="">Select staff…</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->user->name }}
                                @if($member->job_title) ({{ $member->job_title }}) @endif
                                @if($isSuperAdmin && $member->user->salon) — {{ $member->user->salon->name }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('staff_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="section-label block mb-1.5">Start *</label>
                    <input type="datetime-local" name="start" id="appt-start"
                           value="{{ old('start', request('start')) }}"
                           class="input-field @error('start') ring-2 ring-red-400 @enderror">
                    @error('start')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="section-label block mb-1.5">End</label>
                    <input type="datetime-local" name="end" id="appt-end"
                           value="{{ old('end') }}"
                           class="input-field bg-cream-soft/50">
                    <p class="text-xs text-espresso-muted mt-1">Auto-filled from service duration</p>
                </div>
            </div>

            <div>
                <label class="section-label block mb-1.5">Notes</label>
                <textarea name="notes" rows="2" class="input-field" placeholder="Any special requests…">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Book Appointment</button>
                <a href="{{ route('appointments.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
