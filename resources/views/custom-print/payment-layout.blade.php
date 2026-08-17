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
            max-width: 700px;
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
            border: 2px solid #e8edf5;
            background-color: #f8faff;
            overflow: hidden;
            position: relative;
        }
        .logo-img {
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('img/Su-logo.jpeg') }}');
            background-size: cover;
            background-position: center;
        }
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
        .company-info { flex: 1; }
        .company-info h1 {
            font-size: 26px;
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

        .receipt-title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: #1a3a5e;
            letter-spacing: 3px;
            padding: 10px 0;
            border-bottom: 2px dashed #dce3ec;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
            background: #f2f7fd;
            border-radius: 10px;
            padding: 16px 20px;
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

        .amount-section {
            text-align: center;
            padding: 18px 0 12px;
            border-top: 2px solid #e8edf5;
            border-bottom: 2px solid #e8edf5;
            margin-bottom: 16px;
        }
        .amount-number {
            font-size: 32px;
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

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 15px 0;
        }
        .payment-table th {
            background: #1a3a5e;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payment-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ecf0f5;
        }
        .payment-table .text-right {
            text-align: right;
        }

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

        @media print {
            body { background: white !important; padding: 0 !important; }
            .receipt-wrapper { box-shadow: none !important; border: none !important; padding: 20px !important; border-radius: 0 !important; }
            .top-actions { display: none !important; }
            .no-print { display: none !important; }
            .logo-img { display: none !important; }
            .logo-print { display: block !important; }
            .payment-table th { background: #333 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .amount-words { background: #f0f0f0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .info-grid { background: #f5f5f5 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }

        @media (max-width: 576px) {
            .receipt-wrapper { padding: 20px 15px; }
            .info-grid { grid-template-columns: 1fr; }
            .header-section { flex-direction: column; text-align: center; }
            .company-info { text-align: center; }
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

    <div class="header-section">
        <div class="logo-container">
            <div class="logo-img" onerror="this.className='logo-img error';"></div>
            <div class="logo-print">
                <img src="{{ public_path('img/Su-logo.jpeg') }}" alt="S.U Tools">
            </div>
            <div class="logo-fallback">SU</div>
        </div>
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