@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-ts-primary flex items-center justify-center text-white text-xl font-bold shrink-0">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-display text-2xl font-normal text-ts-text">{{ $customer->name }}</h1>
                    @if($customer->flag_type === 'vip')
                        <span class="ts-badge-flag-vip">VIP</span>
                    @elseif($customer->flag_type === 'late')
                        <span class="ts-badge-flag-late">Often Late</span>
                    @elseif($customer->flag_type === 'no_show')
                        <span class="ts-badge-flag-noshow">No-Show Risk</span>
                    @elseif($customer->flag_type === 'flagged')
                        <span class="ts-badge-flag-flagged">Flagged</span>
                    @endif
                </div>
                <p class="text-sm text-ts-text-subtle mt-0.5">
                    {{ $customer->phone ?? '' }}{{ $customer->phone && $customer->email ? ' · ' : '' }}{{ $customer->email ?? '' }}
                </p>
            </div>
        </div>
        <a href="{{ route('customers.edit', $customer) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold border border-ts-border-soft text-ts-text-muted hover:bg-ts-surface-mid transition">
            Edit
        </a>
    </div>

    <div class="grid grid-cols-12 gap-5">

        {{-- Left: Details --}}
        <div class="col-span-12 md:col-span-4 space-y-4">

            {{-- Info card --}}
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-5 space-y-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Details</p>

                @if($customer->date_of_birth)
                <div class="flex items-center justify-between py-2 border-b border-ts-border-soft">
                    <span class="text-xs text-ts-text-subtle">Date of Birth</span>
                    <span class="text-xs font-semibold text-ts-text">{{ $customer->date_of_birth->format('d M Y') }}</span>
                </div>
                @endif

                <div class="flex items-center justify-between py-2 border-b border-ts-border-soft">
                    <span class="text-xs text-ts-text-subtle">Email consent</span>
                    <span class="text-xs font-semibold {{ $customer->consent_email ? 'text-emerald-700' : 'text-ts-text-subtle' }}">
                        {{ $customer->consent_email ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-ts-border-soft">
                    <span class="text-xs text-ts-text-subtle">SMS consent</span>
                    <span class="text-xs font-semibold {{ $customer->consent_sms ? 'text-emerald-700' : 'text-ts-text-subtle' }}">
                        {{ $customer->consent_sms ? 'Yes' : 'No' }}
                    </span>
                </div>
                @if($customer->notes)
                <div class="pt-3 mt-1 border-t border-ts-border-soft">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Notes</p>
                    <p class="text-sm text-ts-text-muted">{{ $customer->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Flag management (owner/super_admin only) --}}
            @if(auth()->user()->hasRole('owner', 'super_admin'))
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-3">Client Flag</p>
                <form method="POST" action="{{ route('customers.flag', $customer) }}" class="space-y-3">
                    @csrf
                    <select name="flag_type" class="input-field text-sm">
                        <option value="">No flag</option>
                        <option value="vip"      {{ $customer->flag_type === 'vip'      ? 'selected' : '' }}>VIP</option>
                        <option value="late"     {{ $customer->flag_type === 'late'     ? 'selected' : '' }}>Often Late</option>
                        <option value="no_show"  {{ $customer->flag_type === 'no_show'  ? 'selected' : '' }}>No-Show Risk</option>
                        <option value="flagged"  {{ $customer->flag_type === 'flagged'  ? 'selected' : '' }}>Flagged</option>
                    </select>
                    <textarea name="flag_notes" rows="2" placeholder="Flag notes…"
                              class="input-field text-sm">{{ $customer->flag_notes }}</textarea>
                    <button type="submit"
                            class="w-full py-2 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition">
                        Save Flag
                    </button>
                </form>
            </div>
            @endif

        </div>

        {{-- Right: Treatment history --}}
        <div class="col-span-12 md:col-span-8">
            <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
                <div class="px-5 py-4 border-b border-ts-border-soft">
                    <h2 class="font-display text-base font-normal text-ts-text">Treatment History</h2>
                </div>

                @if($customer->treatments->isEmpty())
                <div class="px-5 py-12 text-center text-ts-text-subtle text-sm">No treatment history yet.</div>
                @else
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-ts-surface-low">
                            <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Date</th>
                            <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Service</th>
                            <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Staff</th>
                            <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ts-border-soft">
                        @foreach($customer->treatments as $treatment)
                        <tr class="hover:bg-ts-surface-low transition-colors">
                            <td class="px-5 py-3 text-ts-text-subtle">{{ $treatment->date->format('d M Y') }}</td>
                            <td class="px-5 py-3 font-medium text-ts-text">{{ $treatment->service->name }}</td>
                            <td class="px-5 py-3 text-ts-text-muted hidden sm:table-cell">{{ $treatment->staff?->user->name ?? '—' }}</td>
                            <td class="px-5 py-3 font-semibold text-ts-text">€{{ number_format($treatment->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
