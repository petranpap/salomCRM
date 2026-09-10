@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    <div class="flex items-center justify-between print:hidden">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-widest text-ts-text-subtle">Finance</p>
            <h1 class="font-display text-2xl font-normal text-ts-text mt-0.5">Receipt</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.print', $payment) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-ts-primary text-white hover:bg-ts-primary-dim transition shadow-sm">
                Print
            </a>
            <a href="{{ route('payments.index') }}"
               class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-ts-text-muted hover:bg-ts-surface-mid transition">
                Back
            </a>
        </div>
    </div>

    @if($payment->appointment && $payment->appointment->status !== 'completed')
    <div class="px-4 py-3 rounded-card bg-amber-50 border border-amber-200 text-amber-800 text-sm print:hidden">
        Printing will mark this appointment as completed and add it to the client's treatment history.
    </div>
    @endif

    <div class="bg-ts-surface border border-ts-border-soft rounded-card-lg shadow-silk p-8 print:shadow-none print:border-0 print:p-0">

        {{-- Salon header --}}
        <div class="text-center pb-5 border-b-2" style="border-color: {{ $payment->salon?->receiptPrimaryColor() }}">
            @if($payment->salon?->logoUrl())
                <img src="{{ $payment->salon->logoUrl() }}" alt="{{ $payment->salon->name }} logo" class="h-12 mx-auto mb-2 object-contain">
            @endif
            <p class="font-display text-xl text-ts-text">{{ $payment->salon?->name ?? config('app.name') }}</p>
            @if($payment->salon?->address)
                <p class="text-xs text-ts-text-subtle mt-1">{{ $payment->salon->address }}</p>
            @endif
            @if($payment->salon?->phone || $payment->salon?->email)
                <p class="text-xs text-ts-text-subtle">
                    {{ collect([$payment->salon?->phone, $payment->salon?->email])->filter()->join(' · ') }}
                </p>
            @endif
        </div>

        {{-- Receipt meta --}}
        <div class="flex items-center justify-between pt-5 pb-4 text-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Receipt No.</p>
                <p class="font-semibold text-ts-text">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Date</p>
                <p class="font-semibold text-ts-text">{{ ($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between pb-5 border-b border-ts-border-soft text-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Customer</p>
                <p class="font-semibold text-ts-text">{{ $payment->display_customer_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Served By</p>
                <p class="font-semibold text-ts-text">{{ $payment->staff?->name ?? '—' }}</p>
            </div>
        </div>

        {{-- Line item --}}
        <table class="w-full text-sm py-5">
            <thead>
                <tr class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">
                    <th class="text-left pb-2">Description</th>
                    <th class="text-right pb-2">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $baseAmount = $payment->amount - $payment->products_total; @endphp
                @if($baseAmount > 0 || $payment->products->isEmpty())
                <tr class="border-t border-ts-border-soft">
                    <td class="py-3 text-ts-text">
                        {{ $payment->appointment?->service?->name ?? 'Payment' }}
                    </td>
                    <td class="py-3 text-right text-ts-text">€{{ number_format($baseAmount, 2) }}</td>
                </tr>
                @endif
                @foreach($payment->products as $product)
                <tr class="border-t border-ts-border-soft">
                    <td class="py-3 text-ts-text">
                        {{ $product->name }} <span class="text-ts-text-subtle">× {{ $product->pivot->quantity }}</span>
                    </td>
                    <td class="py-3 text-right text-ts-text">€{{ number_format($product->pivot->quantity * $product->pivot->unit_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($payment->salon?->hasVat())
        <div class="space-y-1 pt-4 border-t border-ts-border-soft text-sm">
            <div class="flex items-center justify-between text-ts-text-subtle">
                <p>Subtotal (excl. VAT)</p>
                <p>€{{ number_format($payment->net_amount, 2) }}</p>
            </div>
            <div class="flex items-center justify-between text-ts-text-subtle">
                <p>VAT ({{ rtrim(rtrim(number_format($payment->salon->vat_rate, 2), '0'), '.') }}%)</p>
                <p>€{{ number_format($payment->vat_amount, 2) }}</p>
            </div>
        </div>
        @endif

        <div class="flex items-center justify-between pt-4 {{ $payment->salon?->hasVat() ? '' : 'border-t border-ts-border-soft' }}">
            <p class="font-display text-lg text-ts-text">Total</p>
            <p class="font-display text-2xl" style="color: {{ $payment->salon?->receiptPrimaryColor() }}">€{{ number_format($payment->amount, 2) }}</p>
        </div>

        @if($payment->salon?->vat_number)
        <p class="text-right text-[10px] text-ts-text-subtle mt-1">VAT No. {{ $payment->salon->vat_number }}</p>
        @endif

        <div class="flex items-center justify-between mt-5 pt-4 border-t border-ts-border-soft text-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Method</p>
                <p class="font-medium text-ts-text capitalize">{{ str_replace('_', ' ', $payment->method) }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle">Status</p>
                @if($payment->status === 'paid')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Paid</span>
                @elseif($payment->status === 'pending')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Pending</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-ts-error-light text-ts-error">Refunded</span>
                @endif
            </div>
        </div>

        @if($payment->notes)
        <div class="mt-4 pt-4 border-t border-ts-border-soft">
            <p class="text-[10px] font-bold uppercase tracking-widest text-ts-text-subtle mb-1">Notes</p>
            <p class="text-sm text-ts-text-muted">{{ $payment->notes }}</p>
        </div>
        @endif

        <p class="text-center text-xs text-ts-text-subtle mt-6 pt-4 border-t border-ts-border-soft">
            Thank you for your visit!
        </p>
    </div>
</div>
@endsection
