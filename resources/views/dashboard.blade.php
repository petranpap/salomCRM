@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- ── Page header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Overview</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">
                {{ now()->format('l, F j') }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            @if($lowStockProducts->isNotEmpty())
            <a href="{{ route('products.index') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                {{ $lowStockProducts->count() }} low stock
            </a>
            @endif
        </div>
    </div>

    {{-- ── Row 1: 4 stat cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Today's Revenue --}}
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Today's Revenue</p>
            <p class="font-display text-3xl font-normal text-ts-text mt-2">
                €{{ number_format($todayRevenue, 0) }}
            </p>
            <p class="text-xs text-ts-text-subtle mt-1.5">from {{ $completedToday->count() }} services</p>
        </div>

        {{-- Appointments --}}
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Appointments</p>
            <p class="font-display text-3xl font-normal text-ts-text mt-2">
                {{ $completedToday->count() }}<span class="text-ts-text-subtle text-xl"> / {{ $appointmentsToday->count() }}</span>
            </p>
            <p class="text-xs text-ts-text-subtle mt-1.5">done today</p>
        </div>

        {{-- Active Staff --}}
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Active Staff</p>
            <div class="flex items-center gap-3 mt-2">
                <p class="font-display text-3xl font-normal text-ts-text">{{ $activeStaff->count() }}</p>
                <div class="flex -space-x-2">
                    @foreach($activeStaff->take(4) as $member)
                    <span class="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                          style="background-color: {{ $member->staffProfile?->color ?? '#7d523c' }}"
                          title="{{ $member->name }}">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </span>
                    @endforeach
                </div>
            </div>
            <p class="text-xs text-ts-text-subtle mt-1.5">on shift</p>
        </div>

        {{-- Pending Payments --}}
        <div class="bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk {{ $pendingPayments > 0 ? 'border-amber-200' : '' }}">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Pending Payments</p>
            <div class="flex items-center gap-2 mt-2">
                <p class="font-display text-3xl font-normal {{ $pendingPayments > 0 ? 'text-amber-600' : 'text-ts-text' }}">
                    {{ $pendingPayments }}
                </p>
                @if($pendingPayments > 0)
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                @endif
            </div>
            <p class="text-xs text-ts-text-subtle mt-1.5">awaiting collection</p>
        </div>

    </div>

    {{-- ── Row 2: Timeline (left) + Quick Actions (right) ── --}}
    <div class="grid grid-cols-12 gap-4">

        {{-- Appointment Timeline --}}
        <div class="col-span-12 lg:col-span-8 bg-white border border-ts-border-soft rounded-2xl p-5 shadow-silk">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <h2 class="font-display text-base font-normal text-ts-text">Today's Appointment Timeline</h2>
                    <div class="hidden sm:flex items-center gap-3">
                        @foreach($staffTimeline->take(3) as $member)
                        <span class="flex items-center gap-1.5 text-[11px] text-ts-text-subtle font-medium">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $member['color'] }}"></span>
                            {{ $member['specialty'] ?: explode(' ', $member['name'])[0] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('appointments.index') }}"
                   class="text-xs font-semibold text-ts-primary hover:underline">View all →</a>
            </div>

            @if($staffTimeline->isEmpty())
            <div class="py-16 text-center">
                <p class="text-ts-text-subtle text-sm">No appointments scheduled today.</p>
                <a href="{{ route('appointments.create') }}"
                   class="mt-3 inline-block text-ts-primary text-sm font-semibold hover:underline">
                    Book first appointment →
                </a>
            </div>
            @else
            {{-- Horizontal timeline 08:00 – 20:00 --}}
            <div class="overflow-x-auto -mx-1 px-1">
                @php
                    $startHour = 8; $endHour = 20;
                    $totalMins = ($endHour - $startHour) * 60;
                    $hours     = range($startHour, $endHour - 1);
                @endphp

                <div style="min-width: 640px;">
                    {{-- Hour markers --}}
                    <div class="flex pl-28 pb-2 border-b border-ts-border-soft">
                        @foreach($hours as $h)
                        <div class="flex-1 text-[10px] font-bold text-ts-text-subtle text-center">
                            {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        @endforeach
                    </div>

                    {{-- Staff rows --}}
                    @foreach($staffTimeline as $member)
                    <div class="flex items-center py-2.5 border-b border-ts-border-soft/40 last:border-0">
                        <div class="w-28 shrink-0 pr-3">
                            <p class="text-xs font-semibold text-ts-text truncate">{{ explode(' ', $member['name'])[0] }}</p>
                            @if($member['specialty'])
                            <p class="text-[10px] text-ts-text-subtle truncate">{{ $member['specialty'] }}</p>
                            @endif
                        </div>
                        <div class="flex-1 relative h-10">
                            {{-- Grid lines --}}
                            <div class="absolute inset-0 flex">
                                @foreach($hours as $h)
                                <div class="flex-1 border-l border-ts-border-soft/30"></div>
                                @endforeach
                            </div>
                            {{-- Appointment blocks --}}
                            @foreach($member['appointments'] as $appt)
                            @php
                                $s = \Carbon\Carbon::parse($appt->start);
                                $e = \Carbon\Carbon::parse($appt->end);
                                $left = max(0, ($s->hour*60+$s->minute - $startHour*60) / $totalMins * 100);
                                $width = min(100-$left, $e->diffInMinutes($s) / $totalMins * 100);
                            @endphp
                            <a href="{{ route('appointments.show', $appt->id) }}"
                               class="absolute top-1 bottom-1 rounded-lg flex items-center px-2 text-white text-[10px] font-semibold overflow-hidden hover:brightness-90 transition"
                               style="left:{{ $left }}%; width:{{ max($width,3) }}%; background-color:{{ $member['color'] }};"
                               title="{{ $appt->customer?->name }} · {{ \Carbon\Carbon::parse($appt->start)->format('H:i') }}">
                                <span class="truncate">{{ $appt->customer?->name }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Fallback FullCalendar when no staff profile colors --}}
            @if($staffTimeline->isEmpty() && $appointmentsToday->isNotEmpty())
            <div id="dashboard-calendar" class="mt-2"></div>
            @endif
        </div>

        {{-- Quick Actions panel --}}
        <div class="col-span-12 lg:col-span-4 space-y-3">

            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle px-1">Quick Actions</p>

            {{-- New Booking --}}
            <a href="{{ route('appointments.create') }}"
               class="flex items-center justify-between w-full px-5 py-4 rounded-xl text-sm font-semibold
                      bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-silk">
                <span>New Booking</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </a>

            {{-- New Payment --}}
            @if(Route::has('payments.create'))
            <a href="{{ route('payments.create') }}"
               class="flex items-center justify-between w-full px-5 py-4 rounded-xl text-sm font-semibold
                      bg-ts-secondary-bg text-ts-text-muted border border-ts-border-soft hover:bg-ts-surface-high transition shadow-silk">
                <span>New Payment</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </a>
            @endif

            {{-- View Z Report --}}
            @if(Route::has('z-report.index') && auth()->user()->hasRole('owner','super_admin'))
            <a href="{{ route('z-report.index') }}"
               class="flex items-center justify-between w-full px-5 py-4 rounded-xl text-sm font-semibold
                      bg-white border border-ts-border-soft text-ts-text-muted hover:bg-ts-surface-low transition shadow-silk">
                <span>View Z Report</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </a>
            @endif

            {{-- Alerts: no-shows --}}
            @if($noShowAppointments->isNotEmpty())
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-red-600 mb-2">No-Shows Today</p>
                @foreach($noShowAppointments->take(3) as $appt)
                <div class="flex items-center justify-between py-1.5 border-b border-red-100 last:border-0">
                    <div>
                        <p class="text-xs font-semibold text-ts-text">{{ $appt->customer?->name }}</p>
                        <p class="text-[10px] text-ts-text-subtle">{{ \Carbon\Carbon::parse($appt->start)->format('H:i') }}</p>
                    </div>
                    <a href="{{ route('customers.show', $appt->customer_id) }}"
                       class="text-[10px] font-bold text-red-600 hover:underline">Flag →</a>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Pending confirmations --}}
            @if($pendingAppointments->isNotEmpty())
            <div class="bg-white border border-ts-border-soft rounded-2xl p-4 shadow-silk">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-2">Awaiting Confirmation</p>
                @foreach($pendingAppointments->take(3) as $appt)
                <div class="flex items-center justify-between py-1.5 border-b border-ts-border-soft last:border-0">
                    <div>
                        <p class="text-xs font-semibold text-ts-text">{{ $appt->customer?->name }}</p>
                        <p class="text-[10px] text-ts-text-subtle">{{ \Carbon\Carbon::parse($appt->start)->format('H:i') }}</p>
                    </div>
                    <form method="POST" action="{{ route('appointments.update', $appt->id) }}" class="inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="text-[10px] font-bold text-ts-primary hover:underline">Confirm</button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Low stock --}}
            @if($lowStockProducts->isNotEmpty())
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-amber-700 mb-2">Low Stock</p>
                @foreach($lowStockProducts->take(3) as $product)
                <div class="flex items-center justify-between py-1 border-b border-amber-100 last:border-0">
                    <p class="text-xs font-medium text-ts-text">{{ $product->name }}</p>
                    <span class="text-[10px] font-bold text-amber-700">{{ $product->stock_qty }} left</span>
                </div>
                @endforeach
            </div>
            @endif

        </div>

    </div>

    {{-- ── Super Admin: All Salons Breakdown ── --}}
    @if($isSuperAdmin && $salonBreakdown->isNotEmpty())
    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-ts-border-soft">
            <div>
                <h2 class="font-display text-base font-normal text-ts-text">Platform Overview</h2>
                <p class="text-[11px] text-ts-text-subtle mt-0.5">{{ $totalSalons }} salon{{ $totalSalons !== 1 ? 's' : '' }} on the platform</p>
            </div>
            <a href="{{ route('salons.index') }}"
               class="text-xs font-semibold text-ts-primary hover:underline">Manage Salons →</a>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-ts-border-soft bg-ts-surface-low">
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Salon</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Owner</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Staff</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Clients</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Appts Today</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Revenue Today</th>
                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden lg:table-cell">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @foreach($salonBreakdown as $s)
                <tr class="hover:bg-ts-surface-low transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-ts-primary flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(substr($s->name, 0, 1)) }}
                            </span>
                            <a href="{{ route('salons.show', $s) }}" class="font-semibold text-ts-text hover:text-ts-primary hover:underline">
                                {{ $s->name }}
                            </a>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-ts-text-muted hidden sm:table-cell">{{ $s->owner?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="font-semibold text-ts-text">{{ $s->staff_count }}</span>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="font-semibold text-ts-text">{{ $s->client_count }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="font-semibold text-ts-text">{{ $s->appts_today }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="font-semibold text-ts-text">€{{ number_format($s->revenue_today, 0) }}</span>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        @if($s->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

    {{-- ── Row 3: Recent Payments ── --}}
    @if($recentPayments->isNotEmpty())
    <div class="bg-white border border-ts-border-soft rounded-2xl shadow-silk overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-ts-border-soft">
            <h2 class="font-display text-base font-normal text-ts-text">Recent Payments</h2>
            @if(Route::has('payments.index'))
            <a href="{{ route('payments.index') }}" class="text-xs font-semibold text-ts-primary hover:underline">
                View All Transactions →
            </a>
            @endif
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-ts-surface-low border-b border-ts-border-soft">
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Client</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Service</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Amount</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Method</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @foreach($recentPayments as $payment)
                <tr class="hover:bg-ts-surface-low transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-ts-primary-light flex items-center justify-center text-ts-primary text-xs font-bold shrink-0">
                                {{ strtoupper(substr($payment->display_customer_name, 0, 1)) }}
                            </span>
                            <span class="font-medium text-ts-text">{{ $payment->display_customer_name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-ts-text-muted hidden sm:table-cell">
                        {{ $payment->appointment?->service?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-ts-text">€{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        @if($payment->method === 'card')
                            <svg class="w-5 h-5 text-ts-text-subtle" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        @else
                            <span class="text-xs text-ts-text-muted capitalize">{{ str_replace('_', ' ', $payment->method) }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-ts-text-subtle text-sm hidden md:table-cell">
                        {{ $payment->paid_at?->format('H:i') ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
@if($staffTimeline->isEmpty() && $appointmentsToday->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.SalonCalendar.init('dashboard-calendar', @json($calendarEvents), {
        initialView: 'timeGridDay',
        headerToolbar: false,
        height: 320,
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        businessHours: @json($businessHours),
    });
});
</script>
@endif
@endpush
