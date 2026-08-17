@extends('custom-print.payment-layout')

@section('title', 'Payment Receipt #' . ($payment->ref_no ?? 'N/A'))

@section('content')

<div class="receipt-title">
    @if($payment->is_advance == 1)
        ADVANCE PAYMENT RECEIPT
    @else
        PAYMENT RECEIPT
    @endif
</div>

<div class="info-grid">
    <div class="info-item">
        <span class="label"><i class="fas fa-hashtag"></i> Reference No</span>
        <span class="value">{{ $payment->ref_no ?? 'N/A' }}</span>
    </div>
    <div class="info-item">
        <span class="label"><i class="fas fa-calendar-alt"></i> Paid On</span>
        <span class="value">{{ \Carbon\Carbon::parse($payment->paid_on)->format('l, F d, Y h:i A') }}</span>
    </div>
    <div class="info-item">
        <span class="label"><i class="fas fa-money-bill-alt"></i> Amount</span>
        <span class="value">{{ number_format($payment->amount ?? 0, 0) }}</span>
    </div>
    <div class="info-item">
        <span class="label"><i class="fas fa-credit-card"></i> Payment Method</span>
        <span class="value">{{ \App\Services\PaymentPrintService::getPaymentMethod($payment->method) }}</span>
    </div>
    @if($payment->is_advance == 1)
    <div class="info-item">
        <span class="label"><i class="fas fa-tag"></i> Payment Type</span>
        <span class="value"><span class="label label-warning">Advance Payment</span></span>
    </div>
    @endif
</div>

{{-- Amount in Words --}}
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

{{-- Party Details --}}
@if(!empty($payment->contact) || (!empty($payment->transaction) && !empty($payment->transaction->contact)))
<div class="party-block">
    <div class="party-name">
        <i class="fas fa-user-circle" style="margin-right:8px;"></i>
        @if(!empty($payment->contact))
            {{ $payment->contact->name ?? 'N/A' }}
        @elseif(!empty($payment->transaction->contact))
            {{ $payment->transaction->contact->name ?? 'N/A' }}
        @else
            N/A
        @endif
    </div>
    <div class="party-details">
        @php
            $contact = $payment->contact ?? ($payment->transaction->contact ?? null);
        @endphp
        @if(!empty($contact))
            @if(!empty($contact->contact_address))
                <div><i class="fas fa-map-marker-alt"></i> {{ $contact->contact_address }}</div>
            @endif
            @if(!empty($contact->mobile))
                <div><i class="fas fa-phone"></i> {{ $contact->mobile }}</div>
            @endif
            @if(!empty($contact->tax_number))
                <div><i class="fas fa-id-card"></i> Tax No: {{ $contact->tax_number }}</div>
            @endif
            @if(!empty($contact->email))
                <div><i class="fas fa-envelope"></i> {{ $contact->email }}</div>
            @endif
        @endif
    </div>
</div>
@endif

{{-- Payment Details Table --}}
<table class="payment-table">
    <thead>
        <tr>
            <th style="width:50%;">Detail</th>
            <th style="width:50%;">Information</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Reference No</strong></td>
            <td>{{ $payment->ref_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Amount</strong></td>
            <td>{{ number_format($payment->amount ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td><strong>Payment Method</strong></td>
            <td>{{ \App\Services\PaymentPrintService::getPaymentMethod($payment->method) }}</td>
        </tr>
        @if($payment->is_advance == 1)
        <tr>
            <td><strong>Payment Type</strong></td>
            <td><span class="label label-warning">Advance Payment</span></td>
        </tr>
        @endif
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
        <tr>
            <td><strong>Paid On</strong></td>
            <td>{{ \Carbon\Carbon::parse($payment->paid_on)->format('d-m-Y h:i A') }}</td>
        </tr>
        <tr>
            <td><strong>Payment For</strong></td>
            <td>
                @if($payment->is_advance == 1)
                    Advance Payment
                @elseif(!empty($payment->transaction))
                    {{ \App\Services\PaymentPrintService::getPaymentType($payment->transaction->type ?? '') }}
                    @if($payment->transaction->type == 'sell')
                        (Invoice: {{ $payment->transaction->invoice_no ?? 'N/A' }})
                    @elseif(in_array($payment->transaction->type, ['purchase', 'purchase_return']))
                        (Ref: {{ $payment->transaction->ref_no ?? 'N/A' }})
                    @endif
                @else
                    N/A
                @endif
            </td>
        </tr>
        @if(!empty($payment->created_user))
        <tr>
            <td><strong>Received By</strong></td>
            <td>{{ $payment->created_user->name ?? 'N/A' }}</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- Footer --}}
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