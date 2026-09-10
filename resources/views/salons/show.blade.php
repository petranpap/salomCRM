@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">
                <a href="{{ route('salons.index') }}" class="hover:text-ts-primary transition">All Salons</a>
                <span class="mx-1">·</span> Platform Admin
            </p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">{{ $salon->name }}</h1>
            @if($salon->address)
            <p class="text-sm text-ts-text-muted mt-1">{{ $salon->address }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 mt-1">
            @if($salon->is_active)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Active
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive
                </span>
            @endif
            <a href="{{ route('salons.edit', $salon) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold border border-ts-border-soft bg-white text-ts-text-muted hover:bg-ts-surface-low transition shadow-silk">
                Edit
            </a>
            @if($salon->owner)
            <form method="POST" action="{{ route('salons.reset-onboarding', $salon) }}"
                  onsubmit="return confirm('Reset onboarding for {{ addslashes($salon->name) }}? {{ addslashes($salon->owner->name) }} will be walked through the full setup wizard again from the start on next login.')">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-xs font-semibold border border-ts-border-soft bg-white text-ts-text-muted hover:bg-ts-surface-low transition shadow-silk">
                    Reset Onboarding
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Total Revenue</p>
            <p class="font-display text-3xl font-normal text-ts-text mt-2">€{{ number_format($revenueTotal, 0) }}</p>
            <p class="text-xs text-ts-text-subtle mt-1.5">all time</p>
        </div>
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Today's Revenue</p>
            <p class="font-display text-3xl font-normal text-ts-text mt-2">€{{ number_format($revenueToday, 0) }}</p>
            <p class="text-xs text-ts-text-subtle mt-1.5">{{ now()->format('M j') }}</p>
        </div>
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Staff Members</p>
            <p class="font-display text-3xl font-normal text-ts-text mt-2">{{ $salon->staff_count }}</p>
            <p class="text-xs text-ts-text-subtle mt-1.5">active roles</p>
        </div>
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Clients</p>
            <p class="font-display text-3xl font-normal text-ts-text mt-2">{{ $salon->client_count }}</p>
            <p class="text-xs text-ts-text-subtle mt-1.5">registered</p>
        </div>
    </div>

    {{-- Details + Staff --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Contact info --}}
        <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk p-5 space-y-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Contact</p>
            @if($salon->phone)
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-ts-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-sm text-ts-text">{{ $salon->phone }}</span>
            </div>
            @endif
            @if($salon->email)
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-ts-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-ts-text">{{ $salon->email }}</span>
            </div>
            @endif
            @if($salon->timezone)
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-ts-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm text-ts-text-muted">{{ $salon->timezone }}</span>
            </div>
            @endif
            @if($salon->owner)
            <div class="pt-2 border-t border-ts-border-soft">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-2">Owner</p>
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-full bg-ts-primary flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr($salon->owner->name, 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-ts-text">{{ $salon->owner->name }}</p>
                        <p class="text-[11px] text-ts-text-subtle">{{ $salon->owner->email }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Staff list --}}
        <div class="lg:col-span-2 bg-white border border-ts-border-soft rounded-2xl shadow-silk overflow-hidden">
            <div class="px-5 py-4 border-b border-ts-border-soft">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Staff Members</p>
            </div>
            <div class="divide-y divide-ts-border-soft">
                @forelse($salon->users->where('role', '!=', 'super_admin') as $member)
                <div class="flex items-center justify-between px-5 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-ts-primary-light flex items-center justify-center text-ts-primary text-xs font-bold shrink-0">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-ts-text">{{ $member->name }}</p>
                            <p class="text-[11px] text-ts-text-subtle">{{ $member->email }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-ts-surface-low text-ts-text-muted capitalize">
                        {{ ucfirst($member->role) }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-ts-text-subtle text-sm">No staff members yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
