{{--
    Shared by every receipt template — the items list and the VAT/totals breakdown.
    Kept in one place so VAT correctness (the part that actually matters legally)
    can't drift between templates. Expects $payment and $accentColor.
--}}
<table class="items">
    <thead>
        <tr><th>Description</th><th class="right">Amount</th></tr>
    </thead>
    <tbody>
        @php $baseAmount = $payment->amount - $payment->products_total; @endphp
        @if($baseAmount > 0 || $payment->products->isEmpty())
        <tr>
            <td>{{ $payment->appointment?->service?->name ?? 'Payment' }}</td>
            <td class="right">€{{ number_format($baseAmount, 2) }}</td>
        </tr>
        @endif
        @foreach($payment->products as $product)
        <tr>
            <td>{{ $product->name }} × {{ $product->pivot->quantity }}</td>
            <td class="right">€{{ number_format($product->pivot->quantity * $product->pivot->unit_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    @if($payment->salon?->hasVat())
    <tr>
        <td class="muted">Subtotal (excl. VAT)</td>
        <td class="right">€{{ number_format($payment->net_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="muted">VAT ({{ rtrim(rtrim(number_format($payment->salon->vat_rate, 2), '0'), '.') }}%)</td>
        <td class="right">€{{ number_format($payment->vat_amount, 2) }}</td>
    </tr>
    @endif
    <tr class="total" style="color: {{ $accentColor }};">
        <td>Total</td>
        <td class="right">€{{ number_format($payment->amount, 2) }}</td>
    </tr>
</table>

@if($payment->salon?->vat_number)
<p class="vat-number">VAT No. {{ $payment->salon->vat_number }}</p>
@endif
