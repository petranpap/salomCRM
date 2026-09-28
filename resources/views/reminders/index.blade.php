@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-start justify-between flex-wrap gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Reminder Log</h1>
            <p class="text-xs text-ts-text-subtle mt-1">Every email/SMS reminder attempt — who it was for, and whether it actually sent</p>
        </div>

        <form method="GET" action="{{ route('reminders.index') }}" class="flex items-center gap-2">
            <select name="channel" class="input-field py-2 text-sm w-auto" onchange="this.form.submit()">
                <option value="">All channels</option>
                <option value="mail" {{ request('channel') === 'mail' ? 'selected' : '' }}>Email</option>
                <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
            </select>
            <select name="status" class="input-field py-2 text-sm w-auto" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </form>
    </div>

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk overflow-hidden">
        @if($reminders->isEmpty())
        <div class="px-5 py-10 text-center text-ts-text-subtle text-sm">
            No reminder attempts yet — they're logged here the moment the scheduled
            <code class="text-xs bg-ts-surface-low px-1.5 py-0.5 rounded">appointments:send-reminders</code> command runs.
        </div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-ts-surface-low">
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Attempted</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Channel</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Status</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">To (Customer)</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Service</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Staff</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Salon</th>
                    <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Appointment</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts-border-soft">
                @foreach($reminders as $reminder)
                @php $appointment = $reminder->appointment; @endphp
                <tr class="hover:bg-ts-surface-low transition-colors align-top">
                    <td class="px-5 py-3.5 text-ts-text-muted whitespace-nowrap">
                        {{ $reminder->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="capitalize font-medium text-ts-text">{{ $reminder->channel === 'mail' ? 'Email' : 'SMS' }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($reminder->status === 'sent')
                        <span class="inline-flex items-center gap-1.5 text-ts-success font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-ts-success" aria-hidden="true"></span>
                            Sent
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-ts-error font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-ts-error" aria-hidden="true"></span>
                            Failed
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @if($appointment?->customer)
                        <a href="{{ route('customers.show', $appointment->customer) }}" class="font-medium text-ts-text hover:text-ts-primary">
                            {{ $appointment->customer->name }}
                        </a>
                        <div class="text-xs text-ts-text-subtle mt-0.5">
                            {{ $reminder->channel === 'mail' ? $appointment->customer->email : $appointment->customer->phone }}
                        </div>
                        @else
                        <span class="text-ts-text-subtle">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-ts-text">{{ $appointment?->service?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-ts-text">{{ $appointment?->staffProfile?->user?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-ts-text">{{ $appointment?->salon?->name ?? '—' }}</td>
                    <td class="px-5 py-3.5">
                        @if($appointment)
                        <a href="{{ route('appointments.show', $appointment) }}" class="text-ts-primary hover:underline whitespace-nowrap">
                            {{ $appointment->start->format('d/m/Y H:i') }}
                        </a>
                        @else
                        <span class="text-ts-text-subtle">—</span>
                        @endif
                    </td>
                </tr>
                @if($reminder->status === 'failed' && $reminder->error)
                <tr class="bg-red-50/50">
                    <td colspan="8" class="px-5 py-2 text-xs text-ts-error">
                        <span class="font-semibold">Error:</span> {{ $reminder->error }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="px-5 py-4 border-t border-ts-border-soft">{{ $reminders->links() }}</div>
        @endif
    </div>

</div>
@endsection
