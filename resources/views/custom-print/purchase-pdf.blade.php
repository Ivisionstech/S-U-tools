<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $purchase->ref_no ?? 'N/A' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 30px;
            font-size: 12px;
            color: #1a1a1a;
            background: white;
        }

        /* ===== HEADER ===== */
        .header-section {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 2px solid #1a3a5e;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-container {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e8edf5;
        }
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .logo-fallback {
            width: 70px;
            height: 70px;
            background: #1a3a5e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            border-radius: 8px;
        }
        .company-info {
            flex: 1;
        }
        .company-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 2px;
        }
        .company-info .tagline {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }
        .company-info .address {
            font-size: 11px;
            color: #888;
        }

        /* ===== INVOICE TITLE ===== */
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 4px;
            padding: 10px 0;
            border-bottom: 2px dashed #dce3ec;
            margin-bottom: 20px;
        }

        /* ===== PARTY & INFO ===== */
        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 20px;
        }
        .info-row .party .label {
            font-size: 10px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-row .party .value {
            font-size: 14px;
            font-weight: 600;
            color: #0a1a2b;
            margin-top: 2px;
        }
        .info-row .party .address-text {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .info-row .party .phone-text {
            font-size: 12px;
            color: #666;
        }
        .info-row .details {
            text-align: right;
            font-size: 13px;
            color: #666;
            line-height: 1.8;
            flex-shrink: 0;
        }
        .info-row .details .label {
            font-weight: 600;
            color: #1a2a3a;
        }

        /* ===== ITEMS TABLE ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 15px 0;
        }
        .items-table thead th {
            background: #333;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table thead th:last-child,
        .items-table thead th:nth-child(3),
        .items-table thead th:nth-child(4) {
            text-align: right;
        }
        .items-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }
        .items-table tbody td:last-child,
        .items-table tbody td:nth-child(3),
        .items-table tbody td:nth-child(4) {
            text-align: right;
        }
        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #1a3a5e;
        }
        .items-table .item-name {
            font-weight: 500;
        }
        .items-table .item-desc {
            font-size: 11px;
            color: #888;
            display: block;
        }

        /* ===== TOTALS ===== */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .totals-table {
            width: 100%;
            max-width: 380px;
            font-size: 13px;
        }
        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        .totals-table .label {
            color: #666;
            font-weight: 500;
        }
        .totals-table .value {
            font-weight: 600;
            text-align: right;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #1a3a5e;
            border-bottom: none;
            padding-top: 8px;
            font-size: 16px;
            font-weight: 700;
            color: #1a3a5e;
        }
        .totals-table .grand-total .value {
            font-size: 18px;
            color: #1a3a5e;
        }
        .totals-table .previous-balance td {
            background: #f5f5f5;
            font-weight: 600;
        }
        .totals-table .net-balance td {
            background: #e8e8e8;
            font-weight: 700;
            font-size: 14px;
            color: #1a3a5e;
        }

        /* ===== PAYMENT TABLE ===== */
        .payment-section {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .payment-section h4 {
            font-size: 13px;
            color: #1a2a3a;
            margin-bottom: 8px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .payment-table th {
            text-align: left;
            padding: 5px 8px;
            background: #e8e8e8;
            font-weight: 600;
        }
        .payment-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
        }
        .payment-table .text-right {
            text-align: right;
        }

        /* ===== NOTES ===== */
        .notes-section {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            background: #f5f5f5;
            padding: 10px 14px;
            border-radius: 4px;
        }

        /* ===== FOOTER ===== */
        .invoice-footer {
            border-top: 2px solid #1a3a5e;
            padding-top: 15px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #888;
        }
        .invoice-footer .terms strong {
            color: #1a2a3a;
        }
        .invoice-footer .signature {
            text-align: right;
        }
        .invoice-footer .signature .line {
            width: 150px;
            border-top: 1px solid #333;
            margin-top: 18px;
            margin-bottom: 4px;
        }
        .invoice-footer .signature strong {
            color: #1a2a3a;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

{{-- ===== HEADER ===== --}}
<div class="header-section">
    {{-- Logo - Left Side --}}
    <div class="logo-container">
        @if(file_exists(public_path('img/Su-logo.jpeg')))
            <img src="{{ public_path('img/Su-logo.jpeg') }}" alt="S.U Tools">
        @else
            <div class="logo-fallback">SU</div>
        @endif
    </div>

    {{-- Company Info --}}
    <div class="company-info">
        <h1>S.U TOOLS</h1>
        <div class="tagline">Pakistan</div>
        <div class="address">Gujranwala, Pakistan</div>
    </div>
</div>

{{-- ===== INVOICE TITLE ===== --}}
<div class="invoice-title">ESTIMATE SALE NO {{ $purchase->ref_no ?? 'N/A' }}</div>

{{-- ===== PARTY & INFO ===== --}}
<div class="info-row">
    <div class="party">
        <div class="label">PART NAME</div>
        <div class="value">{{ $purchase->contact->name ?? 'N/A' }}</div>
        @if(!empty($purchase->contact->contact_address))
            <div class="address-text">{{ $purchase->contact->contact_address }}</div>
        @endif
        @if(!empty($purchase->contact->mobile))
            <div class="phone-text">Phone: {{ $purchase->contact->mobile }}</div>
        @endif
    </div>
    <div class="details">
        <div><span class="label">Date :</span> {{ \Carbon\Carbon::parse($purchase->transaction_date)->format('d M Y') }}</div>
        <div><span class="label">Invoice # :</span> {{ $purchase->ref_no ?? 'N/A' }}</div>
        @if(!empty($purchase->location->name))
            <div><span class="label">Location :</span> {{ $purchase->location->name }}</div>
        @endif
    </div>
</div>

{{-- ===== ITEMS TABLE ===== --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:40px;">#</th>
            <th>Item &amp; Description</th>
            <th style="width:80px;">Qty</th>
            <th style="width:100px;">Rate</th>
            <th style="width:110px;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchase->purchase_lines ?? [] as $index => $line)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <span class="item-name">{{ $line->product->name ?? 'N/A' }}</span>
                    @if(!empty($line->variations))
                        <span class="item-desc">{{ $line->variations->name ?? '' }}</span>
                    @endif
                </td>
                <td>{{ number_format($line->quantity ?? 0, 2) }}</td>
                <td>{{ number_format($line->purchase_price_inc_tax ?? $line->purchase_price ?? 0, 0) }}</td>
                <td>{{ number_format((($line->purchase_price_inc_tax ?? $line->purchase_price ?? 0) * ($line->quantity ?? 0)), 0) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;padding:20px;color:#999;">No items found</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ===== TOTALS ===== --}}
@php
    $total_before_tax = 0;
    if(!empty($purchase->purchase_lines)) {
        foreach($purchase->purchase_lines as $line) {
            $total_before_tax += ($line->purchase_price ?? 0) * ($line->quantity ?? 0);
        }
    }
    $discount_amount = 0;
    if(!empty($purchase->discount_type) && !empty($purchase->discount_amount)) {
        if($purchase->discount_type == 'percentage') {
            $discount_amount = ($purchase->discount_amount * $total_before_tax) / 100;
        } else {
            $discount_amount = $purchase->discount_amount ?? 0;
        }
    }
    $final_total = $purchase->final_total ?? $purchase->total ?? 0;
    $previous_balance = 0;
    $net_balance = $final_total + $previous_balance;
@endphp

<div class="totals-section">
    <table class="totals-table">
        <tr><td class="label">Sub Total</td><td class="value">{{ number_format($total_before_tax, 0) }}</td></tr>
        @if($discount_amount > 0)
            <tr><td class="label">Discount</td><td class="value" style="color:#dc3545;">- {{ number_format($discount_amount, 0) }}</td></tr>
        @endif
        @if(!empty($purchase->purchase_tax) && $purchase->purchase_tax > 0)
            <tr><td class="label">Tax</td><td class="value">{{ number_format($purchase->purchase_tax, 0) }}</td></tr>
        @endif
        @if(!empty($purchase->shipping_charges) && $purchase->shipping_charges > 0)
            <tr><td class="label">Shipping</td><td class="value">{{ number_format($purchase->shipping_charges, 0) }}</td></tr>
        @endif
        @if($previous_balance > 0)
            <tr class="previous-balance">
                <td class="label"><strong>PREVIOUS BALANCE</strong></td>
                <td class="value" style="color:#dc3545;">{{ number_format($previous_balance, 0) }}</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td><strong>Total</strong></td>
            <td class="value"><strong>PKR {{ number_format($final_total, 0) }}</strong></td>
        </tr>
        <tr class="net-balance">
            <td><strong>NET Balance</strong></td>
            <td class="value"><strong>PKR {{ number_format($net_balance, 0) }}</strong></td>
        </tr>
    </table>
</div>

{{-- ===== PAYMENT DETAILS ===== --}}
@if(!empty($purchase->payment_lines) && $purchase->payment_lines->count() > 0)
    <div class="payment-section">
        <h4>Payment Details</h4>
        <table class="payment-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th class="text-right">Amount</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->payment_lines as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->paid_on ?? $payment->created_at)->format('d M Y') }}</td>
                        <td>{{ $payment->payment_ref_no ?? '--' }}</td>
                        <td class="text-right">{{ number_format($payment->amount ?? 0, 0) }}</td>
                        <td>{{ \App\Services\PurchasePrintService::getPaymentMethod($payment->method) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- ===== NOTES ===== --}}
@if(!empty($purchase->additional_notes))
    <div class="notes-section">
        <strong>Notes:</strong> {{ $purchase->additional_notes }}
    </div>
@endif

{{-- ===== FOOTER ===== --}}
<div class="invoice-footer">
    <div class="terms">
        <strong>Terms &amp; Conditions:</strong>
        <div>1. Goods once sold cannot be returned.</div>
        <div>2. Subject to local jurisdiction.</div>
    </div>
    <div class="signature">
        <div class="line"></div>
        <div><strong>Authorized Signature</strong></div>
    </div>
</div>

</body>
</html>