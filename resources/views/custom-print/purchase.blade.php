@extends('custom-print.layout')

@section('title', 'Purchase Receipt #' . ($purchase->ref_no ?? 'N/A'))

@section('content')
<div class="receipt-header">
    <div class="logo-container"></div>
    <div class="header-info">
        <h1>Purchase Receipt</h1>
        <span class="ref-no"><i class="fas fa-hashtag"></i> {{ $purchase->ref_no ?? 'N/A' }}</span>
    </div>
</div>

<div class="party-block">
    <div class="party-name">
        <i class="fas fa-user-circle" style="margin-right:8px;"></i>
        {{ $purchase->contact->name ?? 'N/A' }}
    </div>
    <div class="party-details">
        @if(!empty($purchase->contact->contact_address))
            <div><i class="fas fa-map-marker-alt"></i> {{ $purchase->contact->contact_address }}</div>
        @endif
        @if(!empty($purchase->contact->mobile))
            <div><i class="fas fa-phone"></i> {{ $purchase->contact->mobile }}</div>
        @endif
        @if(!empty($purchase->contact->tax_number))
            <div><i class="fas fa-id-card"></i> Tax No: {{ $purchase->contact->tax_number }}</div>
        @endif
    </div>
</div>

<div class="info-grid">
    <div class="info-item">
        <span class="label"><i class="fas fa-calendar-alt"></i> Date</span>
        <span class="value">{{ \Carbon\Carbon::parse($purchase->transaction_date)->format('l, F d, Y') }}</span>
    </div>
    <div class="info-item">
        <span class="label"><i class="fas fa-clock"></i> Time</span>
        <span class="value">{{ \Carbon\Carbon::parse($purchase->transaction_date)->format('h:i A') }}</span>
    </div>
    <div class="info-item">
        <span class="label"><i class="fas fa-store"></i> Location</span>
        <span class="value">{{ $purchase->location->name ?? 'N/A' }}</span>
    </div>
    <div class="info-item">
        <span class="label"><i class="fas fa-tag"></i> Status</span>
        <span class="value">
            <span style="color:#28a745;font-weight:700;">
                {{ ucfirst($purchase->status ?? 'N/A') }}
            </span>
        </span>
    </div>
</div>

<div class="amount-section">
    <div class="amount-number">{{ number_format($purchase->final_total ?? 0, 0) }}</div>
    <div class="amount-words">
        <i class="fas fa-pen-fancy" style="margin-right:8px;"></i>
        {{ \App\Services\PurchasePrintService::numberToWords($purchase->final_total ?? 0) }}
        <span style="font-weight:400;color:#4a607a;font-size:13px;display:block;margin-top:2px;">
            ({{ $purchase->currency_symbol ?? 'PKR' }})
        </span>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th style="width:40px;">#</th>
            <th>Product</th>
            <th style="width:80px;" class="text-right">Qty</th>
            <th style="width:100px;" class="text-right">Unit Price</th>
            <th style="width:110px;" class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchase->purchase_lines ?? [] as $index => $line)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    {{ $line->product->name ?? 'N/A' }}
                    @if(!empty($line->variations))
                        <br><small style="color:#6b7f94;">{{ $line->variations->name ?? '' }}</small>
                    @endif
                </td>
                <td class="text-right">{{ number_format($line->quantity ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($line->purchase_price_inc_tax ?? $line->purchase_price ?? 0, 0) }}</td>
                <td class="text-right" style="font-weight:600;">
                    {{ number_format((($line->purchase_price_inc_tax ?? $line->purchase_price ?? 0) * ($line->quantity ?? 0)), 0) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center" style="padding:20px;color:#6b7f94;">No items found</td>
            </tr>
        @endforelse
    </tbody>
</table>

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
@endphp

<table class="totals-table">
    <tr><td class="label">Subtotal</td><td class="value">{{ number_format($total_before_tax, 0) }}</td></tr>
    @if($discount_amount > 0)
        <tr>
            <td class="label">Discount @if($purchase->discount_type == 'percentage')({{ $purchase->discount_amount }}%)@endif</td>
            <td class="value" style="color:#dc3545;">- {{ number_format($discount_amount, 0) }}</td>
        </tr>
    @endif
    @if(!empty($purchase->purchase_tax) && $purchase->purchase_tax > 0)
        <tr><td class="label">Tax</td><td class="value">{{ number_format($purchase->purchase_tax, 0) }}</td></tr>
    @endif
    @if(!empty($purchase->shipping_charges) && $purchase->shipping_charges > 0)
        <tr><td class="label">Shipping</td><td class="value">{{ number_format($purchase->shipping_charges, 0) }}</td></tr>
    @endif
    <tr class="grand-total">
        <td style="font-weight:700;">Grand Total</td>
        <td style="font-weight:700;font-size:20px;color:#0f3b5e;">{{ number_format($final_total, 0) }}</td>
    </tr>
</table>

@if(!empty($purchase->payment_lines) && $purchase->payment_lines->count() > 0)
    <div style="margin-top:18px;">
        <h4 style="font-size:15px;color:#1a2a3a;margin-bottom:8px;">
            <i class="fas fa-credit-card"></i> Payment Details
        </h4>
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
                @foreach($purchase->payment_lines as $index => $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->paid_on ?? $payment->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $payment->payment_ref_no ?? '--' }}</td>
                        <td class="text-right">{{ number_format($payment->amount ?? 0, 0) }}</td>
                        <td>{{ \App\Services\PurchasePrintService::getPaymentMethod($payment->method) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(!empty($purchase->additional_notes))
    <div style="margin-top:12px;font-size:13px;color:#4a607a;background:#f8faff;padding:12px 16px;border-radius:8px;">
        <strong><i class="fas fa-sticky-note"></i> Notes:</strong> {{ $purchase->additional_notes }}
    </div>
@endif

<div class="receipt-footer">
    <div class="note">
        <i class="fas fa-check-circle" style="color:#28a745;"></i>
        {{ $purchase->business->name ?? 'Business' }}
        <br><small>Thank you for your business!</small>
    </div>
    <div class="barcode">
        @if(!empty($purchase->ref_no))
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2, 40, [39, 48, 54], true) }}" 
                 alt="Barcode" style="max-width:180px;">
            <br><small style="color:#6b7f94;">Ref: {{ $purchase->ref_no }}</small>
        @endif
    </div>
</div>
@endsection