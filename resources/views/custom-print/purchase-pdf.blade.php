<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Receipt #{{ $purchase->ref_no }}</title>
    <style>
        /* Simplified styles for PDF - no background colors, clean print */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 30px;
            font-size: 12px;
            color: #1a1a1a;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 20px; }
        .party {
            background: #f5f5f5;
            padding: 12px;
            margin-bottom: 15px;
            border-left: 3px solid #333;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 20px;
            margin-bottom: 15px;
        }
        .amount {
            text-align: center;
            padding: 15px;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            margin: 15px 0;
        }
        .amount .number {
            font-size: 28px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background: #333;
            color: white;
            padding: 8px;
            text-align: left;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals td {
            padding: 4px 8px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: 700;
            border-top: 2px solid #333;
            padding-top: 8px;
        }
        .footer {
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    {{-- Use the same content as purchase.blade.php but simplified for PDF --}}
    <div class="header">
        <div>
            <h1>Purchase Receipt</h1>
            <div>Ref: #{{ $purchase->ref_no }}</div>
        </div>
        <div style="text-align:right;">
            <div>Date: {{ \Carbon\Carbon::parse($purchase->transaction_date)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="party">
        <strong>{{ $purchase->contact->name ?? 'N/A' }}</strong>
        <div>{{ $purchase->contact->contact_address ?? '' }}</div>
        <div>Phone: {{ $purchase->contact->mobile ?? '' }}</div>
    </div>

    <div class="info-grid">
        <div><strong>Location:</strong> {{ $purchase->location->name ?? 'N/A' }}</div>
        <div><strong>Status:</strong> {{ ucfirst($purchase->status ?? 'N/A') }}</div>
        <div><strong>Payment Status:</strong> {{ ucfirst($purchase->payment_status ?? 'N/A') }}</div>
        <div><strong>Total:</strong> {{ number_format($purchase->final_total ?? 0, 0) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->purchase_lines as $index => $line)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $line->product->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($line->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($line->purchase_price_inc_tax ?? 0, 0) }}</td>
                    <td class="text-right">{{ number_format(($line->purchase_price_inc_tax ?? 0) * ($line->quantity ?? 0), 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        @php
            $total_before_tax = 0;
            foreach($purchase->purchase_lines as $line) {
                $total_before_tax += ($line->purchase_price ?? 0) * ($line->quantity ?? 0);
            }
            $discount_amount = 0;
            if($purchase->discount_type == 'percentage' && $purchase->discount_amount > 0) {
                $discount_amount = ($purchase->discount_amount * $total_before_tax) / 100;
            } else {
                $discount_amount = $purchase->discount_amount ?? 0;
            }
        @endphp
        <tr><td>Subtotal</td><td class="text-right">{{ number_format($total_before_tax, 0) }}</td></tr>
        @if($discount_amount > 0)
            <tr><td>Discount</td><td class="text-right">- {{ number_format($discount_amount, 0) }}</td></tr>
        @endif
        @if(!empty($purchase->shipping_charges) && $purchase->shipping_charges > 0)
            <tr><td>Shipping</td><td class="text-right">{{ number_format($purchase->shipping_charges, 0) }}</td></tr>
        @endif
        <tr class="grand-total">
            <td><strong>Grand Total</strong></td>
            <td class="text-right"><strong>{{ number_format($purchase->final_total ?? 0, 0) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        {{ $purchase->business->name ?? 'Business' }} &bull; Thank you for your business!
    </div>

</body>
</html>