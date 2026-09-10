@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Payments</h1>
        </div>
        <a href="{{ route('payments.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
            + Record Payment
        </a>
    </div>

    {{-- Summary row --}}
    @php
        $totalPaid    = $payments->where('status', 'paid')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->count();
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="ts-stat-card">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">This Page Total</p>
            <p class="font-display text-xl text-ts-text">€{{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="ts-stat-card">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Pending</p>
            <p class="font-display text-xl {{ $totalPending > 0 ? 'text-ts-warning' : 'text-ts-text' }}">{{ $totalPending }}</p>
        </div>
        <div class="ts-stat-card">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Records</p>
            <p class="font-display text-xl text-ts-text">{{ $payments->total() }}</p>
        </div>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-ts-surface-low border-b border-ts-border-soft">
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Client</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden sm:table-cell">Service</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Amount</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden md:table-cell">Method</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Status</th>
                    <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle hidden lg:table-cell">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @forelse($payments as $payment)
                <tr class="hover:bg-ts-surface-low transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-ts-primary-light flex items-center justify-center text-ts-primary text-xs font-bold shrink-0">
                                {{ strtoupper(substr($payment->display_customer_name, 0, 1)) }}
                            </span>
                            <span class="font-medium text-ts-text">
                                {{ $payment->display_customer_name }}
                            </span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-ts-text-muted hidden sm:table-cell">
                        {{ $payment->appointment?->service?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-4 font-semibold text-ts-text">€{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-4 hidden md:table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-ts-surface-high text-ts-text-muted capitalize">
                            {{ str_replace('_', ' ', $payment->method) }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @if($payment->status === 'paid')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Paid</span>
                        @elseif($payment->status === 'pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Pending</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-ts-error-light text-ts-error">Refunded</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-ts-text-subtle text-xs hidden lg:table-cell">
                        {{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y') }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('payments.show', $payment) }}" class="text-xs text-ts-primary hover:underline font-semibold">Receipt</a>
                            <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                                  onsubmit="return confirm('Delete this payment record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-ts-error hover:underline font-semibold">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center text-ts-text-subtle text-sm">
                        No payments recorded yet.
                        <a href="{{ route('payments.create') }}" class="text-ts-primary font-semibold hover:underline ml-1">Record the first →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if($payments->hasPages())
    <div class="pt-1">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
