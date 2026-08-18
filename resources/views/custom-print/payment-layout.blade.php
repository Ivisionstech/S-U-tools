<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Payment Receipt')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body {
            background: #eef2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            padding: 24px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            -webkit-font-smoothing: antialiased;
        }

        .receipt-wrapper {
            max-width: 750px;
            width: 100%;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 32px 40px;
            border: 1px solid #e1e8ed;
        }

        /* ===== ACTION BUTTONS ===== */
        .top-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 24px;
        }
        .top-actions .btn {
            padding: 9px 18px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }
        .top-actions .btn-print { 
            background: #0f172a; 
            color: #ffffff; 
        }
        .top-actions .btn-print:hover { 
            background: #1e293b; 
            transform: translateY(-1px);
        }
        .top-actions .btn-pdf { 
            background: #dc2626; 
            color: #ffffff; 
        }
        .top-actions .btn-pdf:hover { 
            background: #b91c1c; 
            transform: translateY(-1px);
        }
        .top-actions .btn-close { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1;
        }
        .top-actions .btn-close:hover { 
            background: #e2e8f0; 
            color: #0f172a;
        }

        /* ===== HEADER SECTION ===== */
        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            border-bottom: 1.5px solid #000000;
            position: relative;
        }
        .header-left {
            display: flex;
            align-items: center;
            min-width: 120px;
        }
        .header-left img {
            max-height: 85px;
            width: auto;
            object-fit: contain;
        }
        .header-center {
            text-align: center;
            font-size: 16px; /* Increased from 13px */
            font-weight: 700;
            line-height: 1.5;
            color: #000000;
            flex-grow: 1;
            padding: 0 15px;
        }

        /* ===== RECEIPT TITLE ===== */
        .receipt-title {
            text-align: center;
            font-size: 26px; /* Increased from 19px */
            font-weight: 800;
            text-transform: uppercase;
            padding: 12px 0;
            border-bottom: 1.5px solid #000000;
            margin-bottom: 20px;
            letter-spacing: 0.8px;
            color: #000000;
        }

        /* ===== PAYMENT DETAILS ===== */
        .payment-details {
            padding: 4px 0;
        }
        .detail-row {
            display: flex;
            align-items: baseline;
            padding: 5px 0;
            font-size: 14px;
            line-height: 1.45;
        }
        .detail-row .label {
            font-weight: 700;
            width: 160px;
            flex-shrink: 0;
            color: #000000;
        }
        .detail-row .value {
            font-weight: 700;
            color: #000000;
            word-break: break-word;
        }

        /* ===== PRINT STYLES ===== */
        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important; 
            }
            .receipt-wrapper { 
                box-shadow: none !important; 
                border: none !important; 
                padding: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
            }
            .top-actions, .no-print { 
                display: none !important; 
            }
            .header-section, .receipt-title {
                border-color: #000000 !important;
            }
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

    @yield('content')
    
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