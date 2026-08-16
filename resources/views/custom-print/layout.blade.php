<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Purchase Receipt')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* ===== RECEIPT WRAPPER ===== */
        .receipt-wrapper {
            max-width: 650px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            padding: 35px 30px 30px;
            transition: all 0.2s;
        }

        /* ===== HEADER WITH LOGO ===== */
        .receipt-header {
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 2px dashed #dce3ec;
            padding-bottom: 20px;
            margin-bottom: 22px;
        }
        .logo-container {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
            background-image: url('{{ asset('img/Su-logo.jpeg') }}');
            background-size: cover;
            background-position: center;
            border-radius: 50%;
            border: 3px solid #e8edf5;
            background-color: #f8faff;
        }
        .header-info {
            flex: 1;
        }
        .header-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0a1a2b;
            letter-spacing: -0.3px;
        }
        .header-info .ref-no {
            font-size: 14px;
            color: #4a5c6e;
            background: #f0f4fa;
            padding: 2px 14px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 4px;
        }

        /* ===== PARTY BLOCK ===== */
        .party-block {
            background: #f8fbff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 18px;
            border-left: 4px solid #1f4e7a;
        }
        .party-name {
            font-size: 17px;
            font-weight: 700;
            color: #0a1a2b;
        }
        .party-details {
            font-size: 14px;
            color: #3d5a78;
            margin-top: 5px;
            line-height: 1.6;
        }
        .party-details i {
            width: 18px;
            color: #4a7a9c;
        }

        /* ===== INFO GRID ===== */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            background: #f2f7fd;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 18px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }
        .info-item .label {
            color: #4a607a;
            font-weight: 500;
        }
        .info-item .value {
            font-weight: 600;
            color: #0a1a2b;
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

        /* ===== ITEMS TABLE ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 16px 0 18px;
        }
        .items-table th {
            background: #1a3a5e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
        }
        .items-table td {
            padding: 8px 8px;
            border-bottom: 1px solid #ecf0f5;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table tbody tr:hover {
            background: #f8faff;
        }

        /* ===== TOTALS ===== */
        .totals-table {
            width: 100%;
            max-width: 350px;
            margin-left: auto;
            font-size: 14px;
        }
        .totals-table td {
            padding: 4px 8px;
        }
        .totals-table .label {
            color: #4a607a;
        }
        .totals-table .value {
            font-weight: 600;
            text-align: right;
        }
        .totals-table .grand-total {
            font-size: 18px;
            font-weight: 700;
            color: #0f3b5e;
            border-top: 2px solid #1a3a5e;
            padding-top: 8px;
            margin-top: 4px;
        }

        /* ===== PAYMENT TABLE ===== */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 12px 0;
        }
        .payment-table th {
            background: #e8edf5;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            color: #1a2a3a;
        }
        .payment-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #ecf0f5;
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
        .receipt-footer .note {
            flex: 1;
        }
        .receipt-footer .barcode {
            text-align: center;
        }

        /* ===== PRINT BUTTONS ===== */
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-print, .btn-pdf, .btn-close {
            padding: 12px 32px;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            text-decoration: none;
        }
        .btn-print {
            background: #1a3a5e;
            color: white;
        }
        .btn-print:hover {
            background: #0f2a44;
            transform: translateY(-2px);
        }
        .btn-pdf {
            background: #dc3545;
            color: white;
        }
        .btn-pdf:hover {
            background: #b02a37;
            transform: translateY(-2px);
        }
        .btn-close {
            background: #6c757d;
            color: white;
        }
        .btn-close:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .receipt-wrapper {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 20px !important;
            }
            .actions {
                display: none !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-header {
                border-bottom-color: #ccc !important;
            }
            .party-block {
                border-left-color: #333 !important;
            }
            .info-grid {
                background: #f5f5f5 !important;
            }
            .items-table th {
                background: #333 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .amount-words {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            .receipt-wrapper {
                padding: 20px 15px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
            .receipt-header {
                flex-direction: column;
                text-align: center;
            }
            .amount-number {
                font-size: 26px;
            }
            .totals-table {
                max-width: 100%;
            }
            .actions {
                flex-direction: column;
            }
            .btn-print, .btn-pdf, .btn-close {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @yield('extra_styles')
</head>
<body>

<div class="receipt-wrapper" id="receipt-content">
    @yield('content')
</div>

<div class="actions no-print">
    <button class="btn-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Receipt
    </button>
    <button class="btn-pdf" onclick="window.location.href='{{ route('custom.purchase.pdf', $purchase->id ?? 0) }}'">
        <i class="fas fa-file-pdf"></i> Download PDF
    </button>
    <button class="btn-close" onclick="window.close()">
        <i class="fas fa-times"></i> Close
    </button>
</div>

<script>
    // Auto-print if ?print=1 is in URL
    if (window.location.search.includes('print=1')) {
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    }
</script>

</body>
</html>