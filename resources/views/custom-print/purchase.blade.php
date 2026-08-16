@extends('custom-print.layout')

@section('title', 'Invoice #' . ($purchase->ref_no ?? 'N/A'))

@section('content')

{{-- ===== INVOICE TITLE ===== --}}
<div class="invoice-title">
    ESTIMATE SALE NO {{ $purchase->ref_no ?? 'N/A' }}
</div>

{{-- ===== PARTY & INFO ROW ===== --}}
<div class="info-row">
    <div class="party">
        <div class="label">PART NAME</div>
        <div class="value">{{ $purchase->contact->name ?? 'N/A' }}</div>
        @if(!empty($purchase->contact->contact_address))
            <div class="address-text">{{ $purchase->contact->contact_address }}</div>
        @endif
        @if(!empty($purchase->contact->mobile))
            <div class="phone-text"><i class="fas fa-phone" style="margin-right:4px;"></i> {{ $purchase->contact->mobile }}</div>
        @endif
        @if(!empty($purchase->contact->tax_number))
            <div class="phone-text"><i class="fas fa-id-card" style="margin-right:4px;"></i> Tax: {{ $purchase->contact->tax_number }}</div>
        @endif
    </div>
    <div class="details">
        <div><span class="label">Date :</span> {{ \Carbon\Carbon::parse($purchase->transaction_date)->format('d M Y') }}</div>
        <div><span class="label">Invoice # :</span> {{ $purchase->ref_no ?? 'N/A' }}</div>
        @if(!empty($purchase->location->name))
            <div><span class="label">Location :</span> {{ $purchase->location->name }}</div>
        @endif
        @if(!empty($purchase->status))
            <div><span class="label">Status :</span> {{ ucfirst($purchase->status) }}</div>
        @endif
    </div>
</div>

{{-- ===== ITEMS TABLE ===== --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:50px;">#</th>
            <th>Item &amp; Description</th>
            <th style="width:100px;">Qty</th>
            <th style="width:120px;">Rate</th>
            <th style="width:130px;">Amount</th>
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
                    @if(!empty($line->product->sku))
                        <span class="item-desc">SKU: {{ $line->product->sku }}</span>
                    @endif
                </td>
                <td>
                    {{ number_format($line->quantity ?? 0, 2) }}
                    @if(!empty($line->product->unit->short_name))
                        <small style="color:#6b7f94;display:block;font-size:10px;">{{ $line->product->unit->short_name }}</small>
                    @endif
                </td>
                <td>
                    {{ number_format($line->purchase_price_inc_tax ?? $line->purchase_price ?? 0, 0) }}
                </td>
                <td>
                    {{ number_format((($line->purchase_price_inc_tax ?? $line->purchase_price ?? 0) * ($line->quantity ?? 0)), 0) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:30px;color:#6b7f94;">
                    <i class="fas fa-box-open" style="font-size:24px;display:block;margin-bottom:10px;"></i>
                    No items found
                </td>
            </tr>
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
    $previous_balance = 0; // Set this from your business logic if needed
    $net_balance = $final_total + $previous_balance;
@endphp

<div class="totals-section">
    <table class="totals-table">
        <tr>
            <td class="label">Sub Total</td>
            <td class="value">{{ number_format($total_before_tax, 0) }}</td>
        </tr>
        @if($discount_amount > 0)
            <tr>
                <td class="label">Discount @if($purchase->discount_type == 'percentage')({{ $purchase->discount_amount }}%)@endif</td>
                <td class="value" style="color:#dc3545;">- {{ number_format($discount_amount, 0) }}</td>
            </tr>
        @endif
        @if(!empty($purchase->purchase_tax) && $purchase->purchase_tax > 0)
            <tr>
                <td class="label">Tax</td>
                <td class="value">{{ number_format($purchase->purchase_tax, 0) }}</td>
            </tr>
        @endif
        @if(!empty($purchase->shipping_charges) && $purchase->shipping_charges > 0)
            <tr>
                <td class="label">Shipping</td>
                <td class="value">{{ number_format($purchase->shipping_charges, 0) }}</td>
            </tr>
        @endif
        
        {{-- PREVIOUS BALANCE --}}
        @if($previous_balance > 0)
            <tr class="previous-balance">
                <td class="label"><strong>PREVIOUS BALANCE</strong></td>
                <td class="value" style="color:#dc3545;">{{ number_format($previous_balance, 0) }}</td>
            </tr>
        @endif
        
        {{-- GRAND TOTAL --}}
        <tr class="grand-total">
            <td><strong>Total</strong></td>
            <td class="value"><strong>PKR {{ number_format($final_total, 0) }}</strong></td>
        </tr>
        
        {{-- NET BALANCE --}}
        <tr class="net-balance">
            <td><strong>NET Balance</strong></td>
            <td class="value"><strong>PKR {{ number_format($net_balance, 0) }}</strong></td>
        </tr>
    </table>
</div>

{{-- ===== PAYMENT DETAILS ===== --}}
@if(!empty($purchase->payment_lines) && $purchase->payment_lines->count() > 0)
    <div class="payment-section">
        <h4><i class="fas fa-credit-card"></i> Payment Details</h4>
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
        <strong><i class="fas fa-sticky-note"></i> Notes:</strong> {{ $purchase->additional_notes }}
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

@endsection