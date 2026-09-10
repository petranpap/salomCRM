@php
    $primary = $payment->salon?->receiptPrimaryColor() ?? \App\Models\Salon::DEFAULT_RECEIPT_PRIMARY_COLOR;
    $secondary = $payment->salon?->receiptSecondaryColor() ?? \App\Models\Salon::DEFAULT_RECEIPT_SECONDARY_COLOR;
    $logo = $payment->salon?->logoDataUri();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11.5px; color: {{ $secondary }}; }
        h1 { font-size: 16px; margin: 0; font-weight: normal; letter-spacing: 0.5px; }
        .muted { color: #8a877f; }
        .subtle { color: #b0ada4; font-size: 8.5px; text-transform: uppercase; letter-spacing: 1.5px; }
        .right { text-align: right; }
        .header { padding-bottom: 10px; border-bottom: 1px solid {{ $primary }}; margin-bottom: 16px; }
        .header-inner { width: 100%; }
        .header-inner td { vertical-align: middle; }
        .logo { height: 30px; }
        table.meta { width: 100%; margin-bottom: 18px; }
        table.meta td { width: 50%; vertical-align: top; padding-bottom: 10px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items th { text-align: left; font-size: 8.5px; text-transform: uppercase; letter-spacing: 1.5px; color: #b0ada4; border-bottom: 1px solid #ece9e3; padding: 5px 0; font-weight: normal; }
        table.items th.right { text-align: right; }
        table.items td { padding: 7px 0; border-bottom: 1px solid #f5f3ef; }
        table.totals { width: 100%; margin-bottom: 6px; }
        table.totals td { padding: 3px 0; }
        table.totals tr.total td { padding-top: 10px; font-size: 15px; }
        .vat-number { text-align: right; font-size: 8.5px; color: #b0ada4; margin: 0 0 16px 0; }
        table.footer-meta { width: 100%; padding-top: 10px; margin-top: 6px; }
        table.footer-meta td { width: 50%; vertical-align: top; }
        .badge { font-size: 9.5px; font-weight: normal; }
        .thanks { color: #b0ada4; margin-top: 24px; padding-top: 12px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-inner">
            <tr>
                @if($logo)<td style="width: 40px;"><img src="{{ $logo }}" class="logo" alt=""></td>@endif
                <td>
                    <h1>{{ $payment->salon?->name ?? config('app.name') }}</h1>
                    @if($payment->salon?->address || $payment->salon?->phone || $payment->salon?->email)
                    <div class="muted" style="font-size: 9.5px; margin-top: 2px;">
                        {{ collect([$payment->salon?->address, $payment->salon?->phone, $payment->salon?->email])->filter()->join(' · ') }}
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="meta">
        <tr>
            <td>
                <div class="subtle">Receipt No.</div>
                {{ '#' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
            </td>
            <td class="right">
                <div class="subtle">Date</div>
                {{ ($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i') }}
            </td>
        </tr>
        <tr>
            <td>
                <div class="subtle">Customer</div>
                {{ $payment->display_customer_name }}
            </td>
            <td class="right">
                <div class="subtle">Served By</div>
                {{ $payment->staff?->name ?? '—' }}
            </td>
        </tr>
    </table>

    @include('pdf.receipts._items-totals', ['payment' => $payment, 'accentColor' => $secondary])

    <table class="footer-meta">
        <tr>
            <td>
                <div class="subtle">Method</div>
                {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
            </td>
            <td class="right">
                <div class="subtle">Status</div>
                <span class="badge">{{ ucfirst($payment->status) }}</span>
            </td>
        </tr>
    </table>

    @if($payment->notes)
    <div style="margin-top: 10px;">
        <div class="subtle">Notes</div>
        <div class="muted">{{ $payment->notes }}</div>
    </div>
    @endif

    <div class="thanks">Thank you for your visit.</div>
</body>
</html>
