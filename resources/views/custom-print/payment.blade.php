@extends('custom-print.payment-layout')

@section('title', 'Payment Receipt #' . ($payment->ref_no ?? 'N/A'))

@section('content')

{{-- ===== RECEIPT TITLE ===== --}}
<div class="receipt-title">
    @if($payment->is_advance == 1)
        ADVANCE PAYMENT RECEIPT
    @else
        PAYMENT RECEIPT
    @endif
</div>

{{-- ===== PAYMENT INFO ===== --}}
<div class="payment-info">
    {{-- Payment No / Reference No --}}
    <div class="row">
        <span class="label"><i class="fas fa-hashtag"></i> Payment No:</span>
        <span class="value">{{ $payment->ref_no ?? 'N/A' }}</span>
    </div>

    {{-- Party / Customer Name --}}
    @php
        $contact = $payment->contact ?? ($payment->transaction->contact ?? null);
        $partyName = $contact->name ?? 'N/A';
        $partyMobile = $contact->mobile ?? '';
        $partyAddress = $contact->contact_address ?? '';
    @endphp
    <div class="party-name">
        <i class="fas fa-user-circle" style="margin-right:8px;"></i>
        {{ $partyName }}
    </div>
    <div class="party-details">
        @if(!empty($partyAddress))
            <div>{{ $partyAddress }}</div>
        @endif
        @if(!empty($partyMobile))
            <div><i class="fas fa-phone" style="margin-right:6px;"></i> {{ $partyMobile }}</div>
        @endif
        @if(!empty($contact->tax_number))
            <div><i class="fas fa-id-card" style="margin-right:6px;"></i> Tax No: {{ $contact->tax_number }}</div>
        @endif
    </div>

    {{-- Account --}}
    <div class="row" style="margin-top:8px; padding-top:8px; border-top:1px solid #e8edf5;">
        <span class="label"><i class="fas fa-university"></i> Account:</span>
        <span class="value">Cash</span>
    </div>

    {{-- Date --}}
    <div class="row">
        <span class="label"><i class="fas fa-calendar-alt"></i> Date:</span>
        <span class="value">{{ \Carbon\Carbon::parse($payment->paid_on)->format('l, F d, Y h:i A') }}</span>
    </div>
</div>

{{-- ===== AMOUNT ===== --}}
<div class="amount-section">
    <div class="amount-number">{{ number_format($payment->amount ?? 0, 0) }}</div>
    <div class="amount-words">
        <i class="fas fa-pen-fancy" style="margin-right:8px;"></i>
        {{ \App\Services\PaymentPrintService::numberToWords($payment->amount ?? 0) }}
        <span style="font-weight:400;color:#4a607a;font-size:13px;display:block;margin-top:2px;">
            ({{ $payment->currency_symbol ?? 'PKR' }})
        </span>
    </div>
</div>

{{-- ===== ADDITIONAL DETAILS (if payment has extra info) ===== --}}
@if(!empty($payment->cheque_number) || !empty($payment->card_transaction_number) || !empty($payment->bank_account_number) || !empty($payment->note))
<table class="payment-table" style="margin-top:10px;">
    <thead>
        <tr>
            <th style="width:50%;">Detail</th>
            <th style="width:50%;">Information</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($payment->cheque_number))
        <tr>
            <td><strong>Cheque Number</strong></td>
            <td>{{ $payment->cheque_number }}</td>
        </tr>
        @endif
        @if(!empty($payment->card_transaction_number))
        <tr>
            <td><strong>Card Transaction No</strong></td>
            <td>{{ $payment->card_transaction_number }}</td>
        </tr>
        @endif
        @if(!empty($payment->bank_account_number))
        <tr>
            <td><strong>Bank Account No</strong></td>
            <td>{{ $payment->bank_account_number }}</td>
        </tr>
        @endif
        @if(!empty($payment->note))
        <tr>
            <td><strong>Note</strong></td>
            <td>{{ $payment->note }}</td>
        </tr>
        @endif
    </tbody>
</table>
@endif

{{-- ===== FOOTER ===== --}}
<div class="receipt-footer">
    <div class="note">
        <i class="fas fa-check-circle" style="color:#28a745;"></i>
        @if(!empty($payment->transaction) && !empty($payment->transaction->business))
            {{ $payment->transaction->business->name ?? 'Business' }}
        @else
            Business
        @endif
        <br><small>Payment received successfully!</small>
    </div>
    <div class="barcode">
        @if(!empty($payment->ref_no))
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($payment->ref_no, 'C128', 2, 40, [39, 48, 54], true) }}" 
                 alt="Barcode" style="max-width:180px;">
            <br><small style="color:#6b7f94;">Ref: {{ $payment->ref_no }}</small>
        @endif
    </div>
</div>

@endsection