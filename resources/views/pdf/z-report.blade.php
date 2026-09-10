<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Z Report — {{ $reportDate->format('Y-m-d') }}</title>
    <style>
        @page { margin: 36px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a18; }
        h1 { font-size: 20px; margin: 0 0 4px 0; }
        h2 { font-size: 13px; margin: 18px 0 6px 0; padding-bottom: 4px; border-bottom: 1px solid #e5e3de; }
        .muted { color: #6b7068; }
        .subtle { color: #9b9890; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; }
        .right { text-align: right; }
        .header { text-align: center; padding-bottom: 14px; border-bottom: 1px solid #e5e3de; margin-bottom: 14px; }
        table.stats { width: 100%; margin-bottom: 6px; }
        table.stats td { width: 25%; padding: 8px; border: 1px solid #e5e3de; }
        table.stats .big { font-size: 22px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.data th { text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9b9890; border-bottom: 1px solid #e5e3de; padding: 5px 4px; background: #f6f3f0; }
        table.data th.right { text-align: right; }
        table.data td { padding: 6px 4px; border-bottom: 1px solid #f0ede8; }
        .empty { color: #9b9890; padding: 10px 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $salon?->name ?? 'All Salons' }}</h1>
        @if($salon?->address)
            <div class="muted">{{ $salon->address }}</div>
        @endif
        @if($salon?->vat_number)
            <div class="muted">VAT No. {{ $salon->vat_number }}</div>
        @endif
        <div class="muted" style="margin-top: 6px;">Z Report — {{ $reportDate->format('l, F j, Y') }}</div>
        @if($closure)
        <div class="muted" style="margin-top: 2px;">
            Z-{{ $closure->sequence_number }} · Closed by {{ $closure->closedBy?->name ?? 'Unknown' }} on {{ $closure->closed_at->format('d/m/Y H:i') }}
        </div>
        @else
        <div class="muted" style="margin-top: 2px; color: #b45309;">Not yet closed — provisional figures, subject to change</div>
        @endif
    </div>

    <table class="stats">
        <tr>
            <td>
                <div class="subtle">Total Revenue</div>
                <div class="big">€{{ number_format($totalRevenue, 2) }}</div>
                @if($vatCollected > 0)
                <div class="muted" style="font-size: 9px;">incl. €{{ number_format($vatCollected, 2) }} VAT</div>
                @endif
            </td>
            <td>
                <div class="subtle">Cash</div>
                <div class="big">€{{ number_format($cashRevenue, 2) }}</div>
            </td>
            <td>
                <div class="subtle">Card</div>
                <div class="big">€{{ number_format($cardRevenue, 2) }}</div>
            </td>
            <td>
                <div class="subtle">Retail</div>
                <div class="big">€{{ number_format($retailRevenue, 2) }}</div>
            </td>
        </tr>
    </table>

    <table class="stats">
        <tr>
            <td>
                <div class="subtle">Total Appointments</div>
                <div class="big">{{ $totalAppointments }}</div>
            </td>
            <td>
                <div class="subtle">Completed</div>
                <div class="big">{{ $completedAppointments }}</div>
            </td>
            <td>
                <div class="subtle">Avg. Ticket</div>
                <div class="big">€{{ $completedAppointments > 0 ? number_format($totalRevenue / $completedAppointments, 2) : '0.00' }}</div>
            </td>
            <td>
                <div class="subtle">Receipts Issued</div>
                <div class="big">{{ $receiptCount }}</div>
            </td>
        </tr>
    </table>

    @if($refundsTotal > 0)
    <table class="stats">
        <tr>
            <td colspan="4">
                <div class="subtle">Refunds</div>
                <div class="big">€{{ number_format($refundsTotal, 2) }}</div>
            </td>
        </tr>
    </table>
    @endif

    <h2>Breakdown by Technician</h2>
    @if($breakdownByStaff->isEmpty())
        <div class="empty">No payment data for this date.</div>
    @else
    <table class="data">
        <thead><tr><th>Technician</th><th>Appts</th><th class="right">Revenue</th></tr></thead>
        <tbody>
            @foreach($breakdownByStaff as $row)
            <tr>
                <td>{{ $row->staff?->name ?? 'Unknown' }}</td>
                <td>{{ $row->appointment_count }}</td>
                <td class="right">€{{ number_format($row->revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>Breakdown by Service Type</h2>
    @if($breakdownByCategory->isEmpty())
        <div class="empty">No categorised services found.</div>
    @else
    <table class="data">
        <thead><tr><th>Category</th><th class="right">Revenue</th></tr></thead>
        <tbody>
            @foreach($breakdownByCategory as $cat)
            <tr>
                <td>{{ $cat->category_name }}</td>
                <td class="right">€{{ number_format($cat->revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>Breakdown by Product</h2>
    @if($breakdownByProduct->isEmpty())
        <div class="empty">No product sales recorded for this date.</div>
    @else
    <table class="data">
        <thead><tr><th>Product</th><th>Qty Sold</th><th class="right">Revenue</th></tr></thead>
        <tbody>
            @foreach($breakdownByProduct as $row)
            <tr>
                <td>{{ $row->product_name }}</td>
                <td>{{ $row->quantity }}</td>
                <td class="right">€{{ number_format($row->revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
