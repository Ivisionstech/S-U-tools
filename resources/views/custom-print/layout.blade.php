<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invoice')</title>
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
        .invoice-wrapper {
            max-width: 800px;
            width: 100%;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            padding: 40px 40px 30px;
        }

        /* ===== TOP BUTTONS ===== */
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

        /* ========================================== */
        /* ===== HEADER SECTION ===== */
        /* ========================================== */
        .header-section {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 2px solid #1a3a5e;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        /* Logo - Left Side */
        .logo-container {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            border-radius: 8px;
            border: 2px solid #e8edf5;
            background-color: #f8faff;
            overflow: hidden;
            position: relative;
        }
        /* Screen logo */
        .logo-img {
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('img/Su-logo.jpeg') }}');
            background-size: cover;
            background-position: center;
        }
        /* Print logo (hidden on screen) */
        .logo-print {
            display: none;
            width: 100%;
            height: 100%;
        }
        .logo-print img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Fallback if image not found */
        .logo-fallback {
            display: none;
            width: 100%;
            height: 100%;
            background: #1a3a5e;
            color: white;
            font-weight: 700;
            font-size: 20px;
            align-items: center;
            justify-content: center;
        }
        .logo-img.error + .logo-fallback {
            display: flex;
        }

        /* Company Info - Right Side */
        .company-info {
            flex: 1;
        }
        .company-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 2px;
        }
        .company-info .tagline {
            font-size: 13px;
            color: #4a607a;
            font-weight: 500;
        }
        .company-info .address {
            font-size: 12px;
            color: #6b7f94;
            margin-top: 2px;
        }

        /* ========================================== */
        /* ===== INVOICE TITLE ===== */
        /* ========================================== */
        .invoice-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 4px;
            padding: 10px 0;
            border-bottom: 2px dashed #dce3ec;
            margin-bottom: 20px;
        }

        /* ========================================== */
        /* ===== PURCHASE INVOICE SECTION ===== */
        /* ========================================== */
        .purchase-section {
            /* Content area for purchase details */
        }

        /* ===== PARTY & INFO ROW ===== */
        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 20px;
        }
        .info-row .party {
            flex: 1;
        }
        .info-row .party .label {
            font-size: 12px;
            color: #6b7f94;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-row .party .value {
            font-size: 16px;
            font-weight: 600;
            color: #0a1a2b;
            margin-top: 2px;
        }
        .info-row .party .address-text {
            font-size: 13px;
            color: #4a607a;
            margin-top: 4px;
        }
        .info-row .party .phone-text {
            font-size: 13px;
            color: #4a607a;
        }
        .info-row .details {
            text-align: right;
            font-size: 14px;
            color: #3d5a78;
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
            font-size: 14px;
            margin: 15px 0;
        }
        .items-table thead th {
            background: #1a3a5e;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table thead th:last-child,
        .items-table thead th:nth-child(3),
        .items-table thead th:nth-child(4) {
            text-align: right;
        }
        .items-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #ecf0f5;
            vertical-align: middle;
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
            color: #0a1a2b;
        }
        .items-table .item-desc {
            font-size: 12px;
            color: #6b7f94;
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
            font-size: 14px;
        }
        .totals-table td {
            padding: 6px 12px;
            border-bottom: 1px solid #ecf0f5;
        }
        .totals-table .label {
            color: #4a607a;
            font-weight: 500;
        }
        .totals-table .value {
            font-weight: 600;
            text-align: right;
            color: #0a1a2b;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #1a3a5e;
            border-bottom: none;
            padding-top: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #1a3a5e;
        }
        .totals-table .grand-total .value {
            font-size: 20px;
            color: #1a3a5e;
        }
        .totals-table .previous-balance td {
            background: #f8fbff;
            font-weight: 600;
        }
        .totals-table .net-balance td {
            background: #eaf1fb;
            font-weight: 700;
            font-size: 16px;
            color: #1a3a5e;
        }
        .totals-table .net-balance .value {
            font-size: 18px;
            color: #1a3a5e;
        }

        /* ===== PAYMENT TABLE ===== */
        .payment-section {
            margin-top: 20px;
            border-top: 1px solid #ecf0f5;
            padding-top: 15px;
        }
        .payment-section h4 {
            font-size: 14px;
            color: #1a2a3a;
            margin-bottom: 8px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .payment-table th {
            text-align: left;
            padding: 6px 8px;
            background: #f5f7fa;
            font-weight: 600;
        }
        .payment-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ecf0f5;
        }
        .payment-table .text-right {
            text-align: right;
        }

        /* ===== NOTES ===== */
        .notes-section {
            margin-top: 15px;
            font-size: 13px;
            color: #4a607a;
            background: #f8faff;
            padding: 12px 16px;
            border-radius: 6px;
        }

        /* ========================================== */
        /* ===== FOOTER ===== */
        /* ========================================== */
        .invoice-footer {
            border-top: 2px solid #1a3a5e;
            padding-top: 15px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6b7f94;
        }
        .invoice-footer .terms {
            flex: 1;
        }
        .invoice-footer .terms strong {
            color: #1a2a3a;
        }
        .invoice-footer .signature {
            text-align: right;
            flex-shrink: 0;
        }
        .invoice-footer .signature .line {
            width: 150px;
            border-top: 1px solid #1a2a3a;
            margin-top: 20px;
            margin-bottom: 4px;
        }

        /* ========================================== */
        /* ===== PRINT STYLES ===== */
        /* ========================================== */
        @media print {
            body { 
                background: white !important; 
                padding: 0 !important; 
            }
            .invoice-wrapper { 
                box-shadow: none !important; 
                border: none !important; 
                padding: 20px !important; 
                border-radius: 0 !important;
            }
            .top-actions { 
                display: none !important; 
            }
            .no-print { 
                display: none !important; 
            }
            
            /* Show print logo, hide screen logo */
            .logo-img {
                display: none !important;
            }
            .logo-print {
                display: block !important;
            }
            
            .items-table thead th { 
                background: #333 !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .totals-table .previous-balance td { 
                background: #f5f5f5 !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .totals-table .net-balance td { 
                background: #e8e8e8 !important; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .payment-table th {
                background: #e8e8e8 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .notes-section {
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* ========================================== */
        /* ===== RESPONSIVE ===== */
        /* ========================================== */
        @media (max-width: 600px) {
            .invoice-wrapper { padding: 20px 15px; }
            .header-section { flex-direction: column; text-align: center; }
            .company-info { text-align: center; }
            .info-row { flex-direction: column; }
            .info-row .details { text-align: left; }
            .totals-section { justify-content: center; }
            .totals-table { max-width: 100%; }
            .top-actions { justify-content: center; }
            .invoice-footer { flex-direction: column; text-align: center; gap: 15px; }
            .invoice-footer .signature { text-align: center; }
            .invoice-footer .signature .line { margin: 15px auto 4px; }
        }
    </style>
    @yield('extra_styles')
</head>
<body>

<div class="invoice-wrapper" id="invoice-content">
    
    {{-- ===== TOP ACTION BUTTONS ===== --}}
    <div class="top-actions no-print">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <button class="btn btn-pdf" onclick="window.location.href='{{ route('custom.purchase.pdf', $purchase->id ?? 0) }}'">
            <i class="fas fa-file-pdf"></i> Download PDF
        </button>
        <button class="btn btn-close" onclick="window.close()">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    {{-- ========================================== --}}
    {{-- ===== HEADER SECTION ===== --}}
    {{-- ========================================== --}}
    <div class="header-section">
        {{-- Logo - Left Side --}}
        <div class="logo-container">
            {{-- Screen logo --}}
            <div class="logo-img" onerror="this.className='logo-img error';"></div>
            
            {{-- Print logo --}}
            <div class="logo-print">
                <img src="{{ public_path('img/Su-logo.PNG') }}" alt="S.U Tools">
            </div>
            
            {{-- Fallback if image not found --}}
            <div class="logo-fallback">SU</div>
        </div>

        {{-- Company Info - Right Side --}}
        <div class="company-info">
            <h1>S.U TOOLS</h1>
            <div class="tagline">Pakistan</div>
            <div class="address">Gujranwala, Pakistan</div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- ===== PURCHASE INVOICE SECTION ===== --}}
    {{-- ========================================== --}}
    <div class="purchase-section">
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