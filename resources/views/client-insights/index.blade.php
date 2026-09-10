@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-start justify-between flex-wrap gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Client Insights</h1>
            <p class="text-xs text-ts-text-subtle mt-1">No-shows, late arrivals and clients that need attention</p>
        </div>

        <form method="GET" action="{{ route('client-insights.index') }}" class="flex items-center gap-2">
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="input-field py-2 text-sm w-auto">
            <span class="text-xs text-ts-text-subtle">to</span>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="input-field py-2 text-sm w-auto">
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                Apply
            </button>
        </form>
    </div>

    {{-- ── Stat tiles ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-5 shadow-silk">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-ts-success shrink-0" aria-hidden="true"></span>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Completed</p>
            </div>
            <p class="font-display text-3xl text-ts-text">{{ $totals['completed'] }}</p>
        </div>
        <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-5 shadow-silk">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-ts-warning shrink-0" aria-hidden="true"></span>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Late Arrivals</p>
            </div>
            <p class="font-display text-3xl text-ts-text">{{ $totals['late'] }}</p>
        </div>
        <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-5 shadow-silk">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-ts-error shrink-0" aria-hidden="true"></span>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">No-Shows</p>
            </div>
            <p class="font-display text-3xl text-ts-text">{{ $totals['no_show'] }}</p>
        </div>
        <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg p-5 shadow-silk">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-ts-text-subtle shrink-0" aria-hidden="true"></span>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Canceled</p>
            </div>
            <p class="font-display text-3xl text-ts-text">{{ $totals['canceled'] }}</p>
        </div>
    </div>

    {{-- ── Bar chart ── --}}
    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        <div class="px-5 py-4 border-b border-ts-border-soft">
            <h2 class="font-display text-base font-normal text-ts-text">Appointment Outcomes</h2>
            <p class="text-xs text-ts-text-subtle mt-0.5">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>
        </div>

        @php
            $bars = [
                ['label' => 'Completed',  'value' => $totals['completed'], 'color' => 'bg-ts-success'],
                ['label' => 'Late',       'value' => $totals['late'],      'color' => 'bg-ts-warning'],
                ['label' => 'No-Show',    'value' => $totals['no_show'],   'color' => 'bg-ts-error'],
                ['label' => 'Canceled',   'value' => $totals['canceled'],  'color' => 'bg-ts-text-subtle'],
            ];
            $barMax = collect($bars)->max('value') ?: 1;
        @endphp

        @if($barMax === 1 && collect($bars)->sum('value') === 0)
        <div class="px-5 py-10 text-center text-ts-text-subtle text-sm">No appointments in this date range.</div>
        @else
        <div class="p-5 space-y-4">
            @foreach($bars as $bar)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-medium text-ts-text">{{ $bar['label'] }}</span>
                    <span class="text-sm font-semibold text-ts-text">{{ $bar['value'] }}</span>
                </div>
                <div class="w-full h-2 bg-ts-surface-high rounded-full overflow-hidden">
                    <div class="h-full {{ $bar['color'] }} rounded-full transition-all"
                         style="width: {{ round($bar['value'] / $barMax * 100) }}%"
                         title="{{ $bar['label'] }}: {{ $bar['value'] }}"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── Clients to watch ── --}}
    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        <div class="px-5 py-4 border-b border-ts-border-soft">
            <h2 class="font-display text-base font-normal text-ts-text">Clients to Watch</h2>
            <p class="text-xs text-ts-text-subtle mt-0.5">Clients with at least one no-show or late arrival in this range, ranked by severity</p>
        </div>

        @if($clients->isEmpty())
        <div class="px-5 py-10 text-center text-ts-text-subtle text-sm">No clients with no-shows or late arrivals in this range.</div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-ts-surface-low">
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Client</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">No-Shows</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Late</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Visits in Range</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Current Flag</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @foreach($clients as $client)
                <tr class="hover:bg-ts-surface-low transition-colors">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('customers.show', $client) }}" class="font-medium text-ts-text hover:text-ts-primary">
                            {{ $client->name }}
                        </a>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($client->no_show_count > 0)
                        <span class="inline-flex items-center gap-1.5 text-ts-error font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-ts-error" aria-hidden="true"></span>
                            {{ $client->no_show_count }}
                        </span>
                        @else
                        <span class="text-ts-text-subtle">0</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @if($client->late_count > 0)
                        <span class="inline-flex items-center gap-1.5 text-ts-warning font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-ts-warning" aria-hidden="true"></span>
                            {{ $client->late_count }}
                        </span>
                        @else
                        <span class="text-ts-text-subtle">0</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-ts-text">{{ $client->total_count }}</td>
                    <td class="px-5 py-3.5">
                        @if($client->flag_type === 'vip')
                            <span class="ts-badge-flag-vip">VIP</span>
                        @elseif($client->flag_type === 'late')
                            <span class="ts-badge-flag-late">Often Late</span>
                        @elseif($client->flag_type === 'no_show')
                            <span class="ts-badge-flag-noshow">No-Show Risk</span>
                        @elseif($client->flag_type === 'flagged')
                            <span class="ts-badge-flag-flagged">Flagged</span>
                        @else
                            <span class="text-ts-text-subtle">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>

</div>
@endsection
