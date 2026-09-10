@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-espresso-subtle dark:text-espresso-muted mb-1">Appointments</p>
            <h1 class="font-display text-3xl font-semibold text-espresso dark:text-cream">Appointment</h1>
        </div>
        <a href="{{ route('appointments.index') }}" class="btn-outline text-sm">← Back</a>
    </div>
    <div class="card space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="status-{{ $appointment->status }}">{{ ucfirst(str_replace('-', ' ', $appointment->status)) }}</span>
                @if($appointment->was_late)
                    <span class="badge bg-amber-50 text-amber-700">Arrived Late</span>
                @endif
            </div>
            <a href="{{ route('appointments.edit', $appointment) }}" class="text-sm text-rose-sand hover:underline">Edit</a>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-espresso-muted">Customer</span>
                <p class="font-medium"><a href="{{ route('customers.show', $appointment->customer) }}" class="hover:text-rose-sand">{{ $appointment->customer->name }}</a></p>
            </div>
            <div><span class="text-espresso-muted">Service</span><p class="font-medium">{{ $appointment->service->name }}</p></div>
            <div><span class="text-espresso-muted">Staff</span><p class="font-medium">{{ $appointment->staffProfile?->user->name ?? '—' }}</p></div>
            <div><span class="text-espresso-muted">Duration</span><p class="font-medium">{{ $appointment->service->duration_min }} min</p></div>
            <div><span class="text-espresso-muted">Start</span><p class="font-medium">{{ $appointment->start->format('d/m/Y H:i') }}</p></div>
            <div><span class="text-espresso-muted">End</span><p class="font-medium">{{ $appointment->end->format('d/m/Y H:i') }}</p></div>
        </div>
        @if($appointment->notes)
            <div class="pt-2 border-t border-border-warm dark:border-border-warm-dark">
                <span class="text-xs text-espresso-muted uppercase tracking-widest">Notes</span>
                <p class="text-sm mt-1">{{ $appointment->notes }}</p>
            </div>
        @endif

        <div class="pt-4 border-t border-border-warm dark:border-border-warm-dark">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-espresso-muted uppercase tracking-widest">Payments</span>
                <a href="{{ route('payments.create', ['appointment_id' => $appointment->id]) }}"
                   class="text-xs text-rose-sand hover:underline font-semibold">+ Record Payment</a>
            </div>

            @forelse($appointment->payments as $payment)
            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-border-warm dark:border-border-warm-dark' : '' }}">
                <div>
                    <p class="text-sm font-medium">€{{ number_format($payment->amount, 2) }}
                        <span class="text-xs text-espresso-muted capitalize">· {{ str_replace('_', ' ', $payment->method) }}</span>
                    </p>
                    <p class="text-xs text-espresso-muted">
                        @if($payment->status === 'paid') Paid
                        @elseif($payment->status === 'pending') Pending
                        @else Refunded
                        @endif
                        · {{ ($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <a href="{{ route('payments.show', $payment) }}" class="text-sm text-rose-sand hover:underline">View Receipt →</a>
            </div>
            @empty
            <p class="text-sm text-espresso-muted">No payment recorded yet for this appointment.</p>
            @endforelse
        </div>

        @if($appointment->reminders->isNotEmpty())
        <div class="pt-4 border-t border-border-warm dark:border-border-warm-dark">
            <span class="text-xs text-espresso-muted uppercase tracking-widest">Reminders</span>
            @foreach($appointment->reminders as $reminder)
            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-border-warm dark:border-border-warm-dark' : '' }}">
                <span class="text-sm font-medium capitalize">{{ $reminder->channel }}</span>
                <span class="text-xs {{ $reminder->status === 'sent' ? 'text-emerald-700' : 'text-red-600' }}">
                    {{ ucfirst($reminder->status) }}
                    @if($reminder->sent_at) · {{ $reminder->sent_at->format('d/m/Y H:i') }} @endif
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
