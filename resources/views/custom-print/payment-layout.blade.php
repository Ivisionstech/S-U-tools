<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Payment Receipt')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .receipt-wrapper {
            max-width: 650px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            padding: 35px 35px 30px;
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .top-actions .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            text-decoration: none;
        }
        .top-actions .btn-print { background: #1a3a5e; color: white; }
        .top-actions .btn-print:hover { background: #0f2a44; }
        .top-actions .btn-pdf { background: #dc3545; color: white; }
        .top-actions .btn-pdf:hover { background: #b02a37; }
        .top-actions .btn-close { background: #6c757d; color: white; }
        .top-actions .btn-close:hover { background: #5a6268; }

        /* ===== HEADER - Without Logo ===== */
        .header-section {
            text-align: center;
            border-bottom: 2px solid #1a3a5e;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 3px;
        }
        .company-info .tagline {
            font-size: 14px;
            color: #4a607a;
            font-weight: 500;
        }
        .company-info .address {
            font-size: 12px;
            color: #6b7f94;
            margin-top: 2px;
        }

        /* ===== RECEIPT TITLE ===== */
        .receipt-title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 4px;
            padding: 10px 0;
            border-bottom: 2px dashed #dce3ec;
            margin-bottom: 20px;
        }

        /* ===== PAYMENT DETAILS (Payment No, Party, Account, Date) ===== */
        .payment-info {
            background: #f8fbff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 18px;
            border-left: 4px solid #1f4e7a;
        }
        .payment-info .row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
        }
        .payment-info .row .label {
            color: #4a607a;
            font-weight: 500;
        }
        .payment-info .row .value {
            font-weight: 600;
            color: #0a1a2b;
        }
        .payment-info .party-name {
            font-size: 16px;
            font-weight: 700;
            color: #0a1a2b;
            margin-bottom: 4px;
        }
        .payment-info .party-details {
            font-size: 13px;
            color: #3d5a78;
            line-height: 1.6;
        }

        /* ===== AMOUNT SECTION ===== */
        .amount-section {
            text-align: center;
            padding: 18px 0 12px;
            border-top: 2px solid #e8edf5;
            border-bottom: 2px solid #e8edf5;
            margin-bottom: 16px;
        }
        .amount-number {
            font-size: 34px;
            font-weight: 800;
            color: #0f3b5e;
            letter-spacing: 1px;
        }
        .amount-words {
            background: #eaf1fb;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            color: #1a3a5e;
            font-size: 15px;
            margin-top: 10px;
        }

        /* ===== FOOTER ===== */
        .receipt-footer {
            border-top: 2px dashed #dce3ec;
            padding-top: 18px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 12px;
            color: #6b7f94;
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            body { background: white !important; padding: 0 !important; }
            .receipt-wrapper { box-shadow: none !important; border: none !important; padding: 20px !important; border-radius: 0 !important; }
            .top-actions { display: none !important; }
            .no-print { display: none !important; }
            .amount-words { background: #f0f0f0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .payment-info { background: #f5f5f5 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; border-left-color: #333 !important; }
        }

        @media (max-width: 576px) {
            .receipt-wrapper { padding: 20px 15px; }
            .payment-info .row { flex-direction: column; }
            .amount-number { font-size: 26px; }
            .top-actions { justify-content: center; }
        }
    </style>
    @yield('extra_styles')
</head>
<body>
<div class="receipt-wrapper" id="receipt-content">
    
    <div class="top-actions no-print">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <button class="btn btn-pdf" onclick="window.location.href='{{ route('custom.payment.pdf', $payment->id ?? 0) }}'">
            <i class="fas fa-file-pdf"></i> Download PDF
        </button>
        <button class="btn btn-close" onclick="window.close()">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    {{-- ===== HEADER - Without Logo ===== --}}
    <div class="header-section">
        <div class="company-info">
            <h1>S.U TOOLS</h1>
            <div class="tagline">Pakistan</div>
            <div class="address">Gujranwala, Pakistan</div>
        </div>
    </div>

    <div class="content-section">
        @yield('content')
    </div>
    
</div>
<script>
    if (window.location.search.includes('print=1')) {
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    }
</script>
</body>
</html>