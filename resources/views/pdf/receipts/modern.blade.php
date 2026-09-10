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
        @page { margin: 0 40px 36px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: {{ $secondary }}; }
        h1 { font-size: 22px; margin: 0; color: #fff; }
        .muted { color: #6b7068; }
        .subtle { color: #9b9890; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .banner { background: {{ $primary }}; color: #fff; padding: 22px 40px; margin: 0 -40px 20px -40px; }
        .banner-inner { width: 100%; }
        .banner-inner td { vertical-align: middle; }
        .logo { height: 44px; }
        .banner-sub { color: rgba(255,255,255,0.85); font-size: 10px; margin-top: 4px; }
        table.meta { width: 100%; margin-bottom: 14px; }
        table.meta td { width: 50%; vertical-align: top; padding-bottom: 10px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.items th { text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9b9890; border-bottom: 2px solid {{ $primary }}; padding: 6px 0; }
        table.items th.right { text-align: right; }
        table.items td { padding: 8px 0; border-bottom: 1px solid #f0ede8; }
        table.totals { width: 100%; margin-bottom: 6px; }
        table.totals td { padding: 3px 0; }
        table.totals tr.total td { border-top: 2px solid {{ $secondary }}; padding-top: 8px; font-size: 18px; font-weight: bold; }
        .vat-number { text-align: right; font-size: 9px; color: #9b9890; margin: 0 0 14px 0; }
        table.footer-meta { width: 100%; border-top: 1px solid #e5e3de; padding-top: 10px; margin-top: 4px; }
        table.footer-meta td { width: 50%; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-paid { background: #d1fae5; color: #047857; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-refunded { background: #fee2e2; color: #b91c1c; }
        .thanks { text-align: center; color: #9b9890; margin-top: 20px; padding-top: 12px; border-top: 1px solid #e5e3de; }
    </style>
</head>
<body>
    <div class="banner">
        <table class="banner-inner">
            <tr>
                @if($logo)<td style="width: 50px;"><img src="{{ $logo }}" class="logo" alt=""></td>@endif
                <td>
                    <h1>{{ $payment->salon?->name ?? config('app.name') }}</h1>
                    @if($payment->salon?->address || $payment->salon?->phone || $payment->salon?->email)
                    <div class="banner-sub">
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
                <strong>#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</strong>
            </td>
            <td class="right">
                <div class="subtle">Date</div>
                <strong>{{ ($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i') }}</strong>
            </td>
        </tr>
        <tr>
            <td>
                <div class="subtle">Customer</div>
                <strong>{{ $payment->display_customer_name }}</strong>
            </td>
            <td class="right">
                <div class="subtle">Served By</div>
                <strong>{{ $payment->staff?->name ?? '—' }}</strong>
            </td>
        </tr>
    </table>

    @include('pdf.receipts._items-totals', ['payment' => $payment, 'accentColor' => $primary])

    <table class="footer-meta">
        <tr>
            <td>
                <div class="subtle">Method</div>
                <strong>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</strong>
            </td>
            <td class="right">
                <div class="subtle">Status</div>
                <span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
            </td>
        </tr>
    </table>

    @if($payment->notes)
    <div style="margin-top: 10px;">
        <div class="subtle">Notes</div>
        <div class="muted">{{ $payment->notes }}</div>
    </div>
    @endif

    <div class="thanks">Thank you for your visit!</div>
</body>
</html>
