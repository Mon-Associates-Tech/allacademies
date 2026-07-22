<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        .title { font-size: 20px; font-weight: bold; color: #7c3aed; }
        .doc-label { font-size: 14px; font-weight: bold; }
        .muted { color: #64748b; font-size: 10px; }
        .label { text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; color: #64748b; }
        .text-right { text-align: right; }
        .items-table th { background: #f1f5f9; text-align: left; padding: 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .items-table td { padding: 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .totals-table td { padding: 4px 8px; }
        .total-row td { font-weight: bold; font-size: 13px; border-top: 1px solid #1e293b; }
        .badge { display: inline-block; padding: 3px 10px; background: #dcfce7; color: #166534; font-weight: bold; font-size: 10px; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 60%;">
                <div class="title">BookShop</div>
                <div class="muted">{{ $order->branch?->name }}</div>
                @if($order->branch?->address)
                    <div class="muted">{{ $order->branch->address }}</div>
                @endif
                @if($order->branch?->phone)
                    <div class="muted">{{ $order->branch->phone }}</div>
                @endif
            </td>
            <td style="width: 40%;" class="text-right">
                <div class="doc-label">RECEIPT</div>
                <div class="muted">Order: {{ $order->order_number }}</div>
                <div class="muted">Date: {{ ($order->paid_at ?? $order->created_at)->format('M d, Y') }}</div>
                @if($order->payment_reference)
                    <div class="muted">Ref: {{ $order->payment_reference }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table style="margin-top: 24px;">
        <tr>
            <td style="width: 60%;">
                <div class="label">Billed To</div>
                <div style="font-weight: bold;">{{ $order->customer?->name }}</div>
                <div class="muted">{{ $order->customer?->email }}</div>
                @if($order->customer?->phone)
                    <div class="muted">{{ $order->customer->phone }}</div>
                @endif
                <div class="muted">{{ $order->fulfillment_method->label() }}@if($order->isDelivery() && $order->delivery_address) &mdash; {{ $order->delivery_address }}@endif</div>
            </td>
            <td style="width: 40%;" class="text-right">
                <span class="badge">{{ strtoupper($order->payment_status->label()) }}</span>
            </td>
        </tr>
    </table>

    <table class="items-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $item->title_snapshot }}</div>
                        @if($item->author_snapshot)
                            <div class="muted">{{ $item->author_snapshot }}</div>
                        @endif
                    </td>
                    <td class="text-right">GHS {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">GHS {{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 10px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">GHS {{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total {{ $order->isPaid() ? 'Paid' : 'Due' }}</td>
                        <td class="text-right">GHS {{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($order->notes)
        <div style="margin-top: 24px;">
            <div class="label">Notes</div>
            <div>{{ $order->notes }}</div>
        </div>
    @endif

    <div class="footer">
        Thank you for shopping with BookShop &middot; Generated {{ now()->format('M d, Y h:i A') }}
    </div>
</body>
</html>
