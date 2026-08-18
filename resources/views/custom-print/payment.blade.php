@extends('custom-print.payment-layout')

@section('title', 'Payment Receipt #' . ($payment->ref_no ?? 'N/A'))

@section('content')

@php
    $contact = $payment->contact ?? ($payment->transaction->contact ?? null);
    $partyName = $contact->name ?? 'N/A';
    $partyMobile = $contact->mobile ?? '';
    $partyAddress = $contact->contact_address ?? '';
    $partyCity = $contact->city ?? '';
    $partyCountry = $contact->country ?? 'Pakistan';
@endphp

{{-- ===== TOP HEADER ===== --}}
<div class="header-section">
    {{-- Left: Logo --}}
    <div class="header-left">
        <img src="{{ asset('img/Su-logo.PNG') }}" alt="SU Logo">
    </div>

    {{-- Center: Business Info & Payment Header --}}
    <div class="header-center">
        <div>Payment No: {{ $payment->ref_no ?? 'N/A' }}, {{ \Carbon\Carbon::parse($payment->paid_on)->format('l, F d, Y') }}</div>
        <div>{{ $partyName }}</div>
        <div>{{ $partyAddress ? $partyAddress . ' , ' : '' }}{{ $partyCity }}, {{ $partyCountry }}</div>
        <div>, , {{ $partyMobile }}</div>
    </div>
</div>

{{-- ===== RECEIPT TITLE ===== --}}
<div class="receipt-title">
    Payment Receipt
</div>

{{-- ===== PAYMENT DETAILS ===== --}}
<div class="payment-details">
    <div class="detail-row">
        <span class="label">Payment No:</span>
        <span class="value">{{ $payment->ref_no ?? 'N/A' }}</span>
    </div>

    <div class="detail-row">
        <span class="label">Party:</span>
        <span class="value">{{ $partyName }} ( {{ $partyMobile }}, , )</span>
    </div>

    <div class="detail-row">
        <span class="label">Account:</span>
        <span class="value">Cash</span>
    </div>

    <div class="detail-row">
        <span class="label">Date:</span>
        <span class="value">{{ \Carbon\Carbon::parse($payment->paid_on)->format('l, F d, Y g:i A') }}</span>
    </div>

    <div class="detail-row">
        <span class="label">Amount:</span>
        <span class="value">{{ number_format($payment->amount ?? 0, 0) }}</span>
    </div>

    <div class="detail-row">
        <span class="label">Amount in words:</span>
        <span class="value">{{ \App\Services\PaymentPrintService::numberToWords($payment->amount ?? 0) }}</span>
    </div>
</div>

<div style="border-bottom: 1px solid #777; margin-top: 15px;"></div>

@endsection