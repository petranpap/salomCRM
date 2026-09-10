@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <p class="section-label">Appointments</p>
        <h1 class="page-title">Edit Appointment</h1>
    </div>
    <div class="card">
        <form action="{{ route('appointments.update', $appointment) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            @if($isSuperAdmin)
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
                Editing as <strong>Super Admin</strong>
                @if($appointment->salon) — currently assigned to <strong>{{ $appointment->salon->name }}</strong>@endif
            </div>
            @endif

            <div>
                <label class="section-label block mb-1.5">Customer *</label>
                <select name="customer_id" class="input-field @error('customer_id') ring-2 ring-red-400 @enderror">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $appointment->customer_id == $customer->id ? 'selected' : '' }}>
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
                    <select name="service_id" id="appt-service" class="input-field @error('service_id') ring-2 ring-red-400 @enderror">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-duration="{{ $service->duration_min }}"
                                    {{ $appointment->service_id == $service->id ? 'selected' : '' }}>
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
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ $appointment->staff_id == $member->id ? 'selected' : '' }}>
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
                    <div class="relative">
                        <input type="datetime-local" name="start" id="appt-start"
                               value="{{ old('start', $appointment->start->format('Y-m-d\TH:i')) }}"
                               class="input-field pr-10 @error('start') ring-2 ring-red-400 @enderror">
                        <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @error('start')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="section-label block mb-1.5">End *</label>
                    <div class="relative">
                        <input type="datetime-local" name="end" id="appt-end"
                               value="{{ old('end', $appointment->end->format('Y-m-d\TH:i')) }}"
                               class="input-field pr-10 @error('end') ring-2 ring-red-400 @enderror">
                        <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @error('end')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="section-label block mb-1.5">Status</label>
                <select name="status" class="input-field">
                    @foreach(['booked','confirmed','completed','no-show','canceled'] as $s)
                        <option value="{{ $s }}" {{ $appointment->status === $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('-', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="was_late" value="0">
                <input type="checkbox" name="was_late" value="1" id="was_late"
                       {{ old('was_late', $appointment->was_late) ? 'checked' : '' }}
                       class="rounded border-border-warm text-rose-sand focus:ring-rose-sand">
                <label for="was_late" class="text-sm text-espresso-muted cursor-pointer">Customer arrived late</label>
            </div>

            <div>
                <label class="section-label block mb-1.5">Notes</label>
                <textarea name="notes" rows="2" class="input-field">{{ old('notes', $appointment->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Update Appointment</button>
                <a href="{{ route('appointments.show', $appointment) }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
