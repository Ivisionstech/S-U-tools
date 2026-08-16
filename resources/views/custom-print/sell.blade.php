@extends('custom-print.sell-layout')

@section('title', 'Invoice #' . ($sell->ref_no ?? 'N/A'))

@section('content')

<div class="invoice-title">
    SALE INVOICE #{{ $sell->ref_no ?? 'N/A' }}
</div>

<div class="info-row">
    <div class="party">
        <div class="label">CUSTOMER</div>
        <div class="value">{{ $sell->contact->name ?? 'N/A' }}</div>
        @if(!empty($sell->contact->contact_address))
            <div class="address-text">{{ $sell->contact->contact_address }}</div>
        @endif
        @if(!empty($sell->contact->mobile))
            <div class="phone-text"><i class="fas fa-phone" style="margin-right:4px;"></i> {{ $sell->contact->mobile }}</div>
        @endif
        @if(!empty($sell->contact->tax_number))
            <div class="phone-text"><i class="fas fa-id-card" style="margin-right:4px;"></i> Tax: {{ $sell->contact->tax_number }}</div>
        @endif
    </div>
    <div class="details">
        <div><span class="label">Date :</span> {{ \Carbon\Carbon::parse($sell->transaction_date)->format('d M Y') }}</div>
        <div><span class="label">Invoice # :</span> {{ $sell->ref_no ?? 'N/A' }}</div>
        @if(!empty($sell->location->name))
            <div><span class="label">Location :</span> {{ $sell->location->name }}</div>
        @endif
        @if(!empty($sell->status))
            <div><span class="label">Status :</span> {{ ucfirst($sell->status) }}</div>
        @endif
    </div>
</div>

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
        @forelse($sell->sell_lines ?? [] as $index => $line)
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
                    {{ number_format($line->unit_price_inc_tax ?? $line->unit_price ?? 0, 0) }}
                </td>
                <td>
                    {{ number_format((($line->unit_price_inc_tax ?? $line->unit_price ?? 0) * ($line->quantity ?? 0)), 0) }}
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

@php
    $total_before_tax = 0;
    if(!empty($sell->sell_lines)) {
        foreach($sell->sell_lines as $line) {
            $total_before_tax += ($line->unit_price ?? 0) * ($line->quantity ?? 0);
        }
    }
    $discount_amount = 0;
    if(!empty($sell->discount_type) && !empty($sell->discount_amount)) {
        if($sell->discount_type == 'percentage') {
            $discount_amount = ($sell->discount_amount * $total_before_tax) / 100;
        } else {
            $discount_amount = $sell->discount_amount ?? 0;
        }
    }
    $final_total = $sell->final_total ?? $sell->total ?? 0;
    $total_paid = $sell->payment_lines->sum('amount') ?? 0;
    $net_balance = $final_total - $total_paid;
@endphp

<div class="totals-section">
    <table class="totals-table">
        <tr>
            <td class="label">Sub Total</td>
            <td class="value">{{ number_format($total_before_tax, 0) }}</td>
        </tr>
        @if($discount_amount > 0)
            <tr>
                <td class="label">Discount @if($sell->discount_type == 'percentage')({{ $sell->discount_amount }}%)@endif</td>
                <td class="value" style="color:#dc3545;">- {{ number_format($discount_amount, 0) }}</td>
            </tr>
        @endif
        @if(!empty($sell->sell_tax) && $sell->sell_tax > 0)
            <tr>
                <td class="label">Tax</td>
                <td class="value">{{ number_format($sell->sell_tax, 0) }}</td>
            </tr>
        @endif
        @if(!empty($sell->shipping_charges) && $sell->shipping_charges > 0)
            <tr>
                <td class="label">Shipping</td>
                <td class="value">{{ number_format($sell->shipping_charges, 0) }}</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td><strong>Total</strong></td>
            <td class="value"><strong>PKR {{ number_format($final_total, 0) }}</strong></td>
        </tr>
        <tr class="net-balance">
            <td><strong>Balance Due</strong></td>
            <td class="value"><strong>PKR {{ number_format($net_balance, 0) }}</strong></td>
        </tr>
    </table>
</div>

@if(!empty($sell->payment_lines) && $sell->payment_lines->count() > 0)
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
                @foreach($sell->payment_lines as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->paid_on ?? $payment->created_at)->format('d M Y') }}</td>
                        <td>{{ $payment->payment_ref_no ?? '--' }}</td>
                        <td class="text-right">{{ number_format($payment->amount ?? 0, 0) }}</td>
                        <td>{{ \App\Services\SellPrintService::getPaymentMethod($payment->method) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(!empty($sell->additional_notes))
    <div class="notes-section">
        <strong><i class="fas fa-sticky-note"></i> Notes:</strong> {{ $sell->additional_notes }}
    </div>
@endif

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