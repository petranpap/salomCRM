@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <p class="section-label">Staff</p>
        <h1 class="page-title">Edit {{ $staff->name }}</h1>
    </div>

    <div class="card">
        <form action="{{ route('staff.update', $staff) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="section-label block mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $staff->name) }}"
                           class="input-field @error('name') ring-2 ring-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="section-label block mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $staff->email) }}"
                           class="input-field @error('email') ring-2 ring-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="section-label block mb-1.5">System Role *</label>
                    <select name="role" class="input-field @error('role') ring-2 ring-red-400 @enderror">
                        <option value="staff" {{ old('role', $staff->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="owner" {{ old('role', $staff->role) === 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                    <p class="text-[11px] text-espresso-muted mt-1">Controls app access — use Job Title below for their actual position.</p>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="section-label block mb-1.5">Job Title</label>
                    <input type="text" name="job_title"
                           value="{{ old('job_title', $staff->staffProfile?->job_title) }}"
                           placeholder="e.g. Manager, Receptionist, Senior Stylist…"
                           class="input-field @error('job_title') ring-2 ring-red-400 @enderror">
                    @error('job_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="section-label block mb-1.5">New Password <span class="normal-case font-normal">(leave blank to keep current)</span></label>
                    <input type="password" name="password"
                           class="input-field @error('password') ring-2 ring-red-400 @enderror">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="section-label block mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input-field">
                </div>

                <div>
                    <label class="section-label block mb-1.5">Specialty</label>
                    <input type="text" name="specialty"
                           value="{{ old('specialty', $staff->staffProfile?->specialty) }}"
                           placeholder="e.g. Highlights, Nails…"
                           class="input-field @error('specialty') ring-2 ring-red-400 @enderror">
                    @error('specialty')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="section-label block mb-1.5">Calendar Colour</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color"
                               value="{{ old('color', $staff->staffProfile?->color ?? '#C9907A') }}"
                               class="w-12 h-10 rounded-xl border border-border-warm cursor-pointer bg-transparent">
                        <span class="text-xs text-espresso-muted">Shown on appointment calendar</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <label class="flex items-center gap-2 text-sm text-espresso-muted cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $staff->staffProfile?->is_active ?? true) ? 'checked' : '' }}
                           class="rounded border-border-warm text-rose-sand focus:ring-rose-sand">
                    Active (can be assigned to appointments)
                </label>
            </div>

            <x-weekly-schedule field-name="working_hours" label="Working Hours"
                :hours="$staff->staffProfile?->normalizedWorkingHours() ?? []" :limits="$salonHours ?? null" />

            @if(auth()->user()->isSuperAdmin() && $salons->isNotEmpty())
            <div class="col-span-2">
                <label class="section-label block mb-1.5">Salon</label>
                <select name="salon_id" class="input-field">
                    <option value="">— No Salon —</option>
                    @foreach($salons as $salon)
                        <option value="{{ $salon->id }}" {{ old('salon_id', $staff->salon_id) == $salon->id ? 'selected' : '' }}>
                            {{ $salon->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('staff.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

    @if(auth()->user()->isSuperAdmin() && $staff->isLocked())
    <div class="mt-6 card border-amber-200 dark:border-amber-900/50">
        <h3 class="text-sm font-semibold text-amber-700 mb-1">Account Locked</h3>
        <p class="text-xs text-espresso-muted mb-3">
            Too many failed login attempts — locked until {{ $staff->locked_until->format('d/m/Y H:i') }}.
        </p>
        <form action="{{ route('staff.unlock', $staff) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium text-amber-700 border border-amber-200 hover:bg-amber-50 transition-colors">
                Unlock Account
            </button>
        </form>
    </div>
    @endif

    {{-- Danger zone --}}
    @if(Auth::id() !== $staff->id)
    <div class="mt-6 card border-red-200 dark:border-red-900/50">
        <h3 class="text-sm font-semibold text-red-600 mb-3">Danger Zone</h3>
        <form action="{{ route('staff.destroy', $staff) }}" method="POST"
              onsubmit="return confirm('Remove {{ addslashes($staff->name) }} from the salon?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 transition-colors">
                Remove Staff Member
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
