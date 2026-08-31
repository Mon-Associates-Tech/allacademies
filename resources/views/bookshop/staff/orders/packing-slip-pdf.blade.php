<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        .title { font-size: 20px; font-weight: bold; }
        .muted { color: #64748b; font-size: 10px; }
        .label { text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; color: #64748b; }
        .text-right { text-align: right; }
        .items-table th { background: #f1f5f9; text-align: left; padding: 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .checkbox-cell { width: 40px; text-align: center; }
        .checkbox { display: inline-block; width: 14px; height: 14px; border: 1.5px solid #1e293b; }
        .qty-cell { font-weight: bold; font-size: 14px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #1e293b; width: 220px; padding-top: 4px; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 60%;">
                <div class="title">Packing Slip</div>
                <div class="muted">{{ $order->branch?->name }}</div>
            </td>
            <td style="width: 40%;" class="text-right">
                <div style="font-weight: bold; font-size: 14px;">{{ $order->order_number }}</div>
                <div class="muted">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                <div class="muted">Payment: {{ $order->payment_status->label() }}</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 24px;">
        <tr>
            <td>
                <div class="label">Customer</div>
                <div style="font-weight: bold;">{{ $order->customer?->name }}</div>
                @if($order->customer?->phone)
                    <div class="muted">{{ $order->customer->phone }}</div>
                @endif
            </td>
            <td class="text-right">
                <div class="label">Fulfillment</div>
                <div style="font-weight: bold; font-size: 14px;">{{ $order->isDelivery() ? 'DELIVERY' : 'PICKUP' }}</div>
            </td>
        </tr>
    </table>

    @if($order->isDelivery() && $order->delivery_address)
        <table style="margin-top: 12px; border: 1.5px solid #1e293b;">
            <tr>
                <td style="padding: 10px;">
                    <div class="label">Deliver To</div>
                    <div style="font-weight: bold;">{{ $order->delivery_address }}</div>
                </td>
            </tr>
        </table>
    @endif

    <table class="items-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th class="checkbox-cell">Packed</th>
                <th>Item</th>
                <th class="text-right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="checkbox-cell"><span class="checkbox"></span></td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->title_snapshot }}</div>
                        @if($item->author_snapshot)
                            <div class="muted">{{ $item->author_snapshot }}</div>
                        @endif
                        @if($item->book?->isbn)
                            <div class="muted">ISBN: {{ $item->book->isbn }}</div>
                        @endif
                    </td>
                    <td class="text-right qty-cell">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->notes)
        <div style="margin-top: 24px;">
            <div class="label">Order Notes</div>
            <div>{{ $order->notes }}</div>
        </div>
    @endif

    <div class="signature-line">Packed by / Date</div>
</body>
</html>
