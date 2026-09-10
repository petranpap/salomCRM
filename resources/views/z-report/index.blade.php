@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">
                Z Report — {{ $reportDate->format('l, F j, Y') }}
            </h1>
            @if($closure)
            <p class="text-xs text-ts-success font-semibold mt-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Closed as Z-{{ $closure->sequence_number }} by {{ $closure->closedBy?->name ?? 'Unknown' }} on {{ $closure->closed_at->format('d/m/Y H:i') }} — this day is locked.
            </p>
            @else
            <p class="text-xs text-ts-text-subtle mt-1">Daily closing summary and performance breakdown</p>
            @endif
        </div>

        <div class="flex items-center gap-3 flex-wrap print:hidden">
            {{-- Date picker --}}
            <form method="GET" action="{{ route('z-report.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $reportDate->toDateString() }}"
                       class="input-field py-2 text-sm w-auto"
                       onchange="this.form.submit()">
            </form>

            {{-- Close day --}}
            @if($closure)
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-ts-success-light text-ts-success">
                Closed · Z-{{ $closure->sequence_number }}
            </span>
            @else
            <form method="POST" action="{{ route('z-report.close') }}"
                  onsubmit="return confirm('Close {{ $reportDate->format('d/m/Y') }}? This permanently locks all payments and appointments for that day — it cannot be undone.');">
                @csrf
                <input type="hidden" name="date" value="{{ $reportDate->toDateString() }}">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                    Close Day
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- ── Top stats row ── --}}
    <div class="grid grid-cols-12 gap-4">

        {{-- Total revenue (large) --}}
        <div class="col-span-12 md:col-span-8 bg-ts-surface border border-ts-border-soft rounded-card-lg p-6 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-2">Total Revenue</p>
            <p class="font-display text-5xl font-normal text-ts-text">€{{ number_format($totalRevenue, 2) }}</p>
            @if($vatCollected > 0)
            <p class="text-xs text-ts-text-subtle mt-1">incl. €{{ number_format($vatCollected, 2) }} VAT</p>
            @endif

            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="flex items-center gap-3 p-4 bg-ts-surface-low rounded-xl">
                    <svg class="w-5 h-5 text-ts-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Cash Revenue</p>
                        <p class="font-display text-xl text-ts-text">€{{ number_format($cashRevenue, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-ts-surface-low rounded-xl">
                    <svg class="w-5 h-5 text-ts-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Card Revenue</p>
                        <p class="font-display text-xl text-ts-text">€{{ number_format($cardRevenue, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-ts-surface-low rounded-xl">
                    <svg class="w-5 h-5 text-ts-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 11H4L5 9z"/>
                    </svg>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Retail Revenue</p>
                        <p class="font-display text-xl text-ts-text">€{{ number_format($retailRevenue, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-6 mt-4 pt-4 border-t border-ts-border-soft flex-wrap">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Total Appointments</p>
                    <p class="text-lg font-semibold text-ts-text mt-0.5">{{ $totalAppointments }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Completed</p>
                    <p class="text-lg font-semibold text-emerald-700 mt-0.5">{{ $completedAppointments }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Avg. Ticket</p>
                    <p class="text-lg font-semibold text-ts-text mt-0.5">
                        €{{ $completedAppointments > 0 ? number_format($totalRevenue / $completedAppointments, 2) : '0.00' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Receipts Issued</p>
                    <p class="text-lg font-semibold text-ts-text mt-0.5">{{ $receiptCount }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Refunds</p>
                    <p class="text-lg font-semibold {{ $refundsTotal > 0 ? 'text-ts-error' : 'text-ts-text' }} mt-0.5">
                        €{{ number_format($refundsTotal, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tips / performance card --}}
        <div class="col-span-12 md:col-span-4 bg-ts-primary rounded-card-lg p-6 shadow-silk-md flex flex-col justify-between">
            <div>
                <svg class="w-8 h-8 text-white/60 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <p class="text-white/70 text-[10px] font-bold uppercase tracking-widest mb-1">Performance</p>
                <p class="font-display text-white text-2xl">
                    @if($completedAppointments >= $totalAppointments * 0.8)
                        Excellent
                    @elseif($completedAppointments >= $totalAppointments * 0.5)
                        Good
                    @else
                        Needs Review
                    @endif
                </p>
            </div>
            <p class="text-white/60 text-sm mt-4">
                {{ $completedAppointments }} of {{ $totalAppointments }} appointments completed
                @if($totalAppointments > 0)
                ({{ round($completedAppointments / $totalAppointments * 100) }}%)
                @endif
            </p>
        </div>
    </div>

    {{-- ── Breakdowns ── --}}
    <div class="grid grid-cols-12 gap-4">

        {{-- By Technician --}}
        <div class="col-span-12 md:col-span-6 bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
            <div class="px-5 py-4 border-b border-ts-border-soft flex items-center justify-between">
                <h2 class="font-display text-base font-normal text-ts-text">Breakdown by Technician</h2>
            </div>

            @if($breakdownByStaff->isEmpty())
            <div class="px-5 py-10 text-center text-ts-text-subtle text-sm">No payment data for this date.</div>
            @else
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-ts-surface-low">
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Technician</th>
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Appts</th>
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ts-border-soft">
                    @foreach($breakdownByStaff as $row)
                    <tr class="hover:bg-ts-surface-low transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full bg-ts-primary flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($row->staff?->name ?? '?', 0, 1)) }}
                                </span>
                                <div>
                                    <p class="font-medium text-ts-text">{{ $row->staff?->name ?? 'Unknown' }}</p>
                                    <p class="text-[10px] text-ts-text-subtle capitalize">{{ $row->staff?->staffProfile?->specialty ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-ts-text">{{ $row->appointment_count }}</td>
                        <td class="px-5 py-3.5 font-semibold text-ts-text">€{{ number_format($row->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @endif
        </div>

        {{-- By Service Category --}}
        <div class="col-span-12 md:col-span-6 bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
            <div class="px-5 py-4 border-b border-ts-border-soft">
                <h2 class="font-display text-base font-normal text-ts-text">Breakdown by Service Type</h2>
            </div>

            @if($breakdownByCategory->isEmpty())
            <div class="px-5 py-10 text-center text-ts-text-subtle text-sm">No categorised services found.</div>
            @else
            <div class="p-5 space-y-4">
                @php $catMax = $breakdownByCategory->max('revenue') ?: 1; @endphp
                @foreach($breakdownByCategory as $cat)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-ts-text">{{ $cat->category_name }}</span>
                        <span class="text-sm font-semibold text-ts-text">€{{ number_format($cat->revenue, 2) }}</span>
                    </div>
                    <div class="w-full h-2 bg-ts-surface-high rounded-full overflow-hidden">
                        <div class="h-full bg-ts-primary rounded-full transition-all"
                             style="width: {{ round($cat->revenue / $catMax * 100) }}%"></div>
                    </div>
                </div>
                @endforeach

                @php
                    $topCat = $breakdownByCategory->sortByDesc('revenue')->first();
                @endphp
                @if($topCat)
                <div class="pt-3 mt-2 border-t border-ts-border-soft flex items-center justify-between text-xs text-ts-text-subtle">
                    <span>Top Performer: <strong class="text-ts-text">{{ $topCat->category_name }}</strong></span>
                    <span>€{{ number_format($totalRevenue / max($totalAppointments, 1), 2) }} avg ticket</span>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- By Product (retail) --}}
        <div class="col-span-12 bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
            <div class="px-5 py-4 border-b border-ts-border-soft flex items-center justify-between">
                <h2 class="font-display text-base font-normal text-ts-text">Breakdown by Product</h2>
                <span class="text-xs text-ts-text-subtle">Retail total: €{{ number_format($retailRevenue, 2) }}</span>
            </div>

            @if($breakdownByProduct->isEmpty())
            <div class="px-5 py-10 text-center text-ts-text-subtle text-sm">No product sales recorded for this date.</div>
            @else
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-ts-surface-low">
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Product</th>
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Qty Sold</th>
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ts-border-soft">
                    @foreach($breakdownByProduct as $row)
                    <tr class="hover:bg-ts-surface-low transition-colors">
                        <td class="px-5 py-3.5 font-medium text-ts-text">{{ $row->product_name }}</td>
                        <td class="px-5 py-3.5 text-ts-text">{{ $row->quantity }}</td>
                        <td class="px-5 py-3.5 font-semibold text-ts-text">€{{ number_format($row->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Print ── --}}
    <div class="flex items-center justify-center gap-3 pt-2 print:hidden">
        <a href="{{ route('z-report.print', request()->only('date')) }}" target="_blank"
           class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Report
        </a>
    </div>

</div>
@endsection
