<?php

namespace App\Http\Controllers;

use App\Services\PurchasePrintService;
use Illuminate\Http\Request;

class CustomPurchasePrintController extends Controller
{
    /**
     * Display custom purchase receipt
     */
    public function print($id)
    {
        try {
            $purchase = PurchasePrintService::getPurchaseData($id);
            $totals = PurchasePrintService::calculateTotals($purchase);
            
            return view('custom-print.purchase', compact('purchase', 'totals'));
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Custom Print Error: ' . $e->getMessage());
            abort(404, 'Purchase not found. Error: ' . $e->getMessage());
        }
    }

    /**
     * Preview before printing
     */
    public function preview($id)
    {
        return $this->print($id);
    }

    /**
     * Download as PDF
     */
    public function downloadPdf($id)
    {
        try {
            $purchase = PurchasePrintService::getPurchaseData($id);
            $totals = PurchasePrintService::calculateTotals($purchase);
            
            // Check if DOMPDF is installed
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('custom-print.purchase-pdf', compact('purchase', 'totals'));
                return $pdf->download('purchase-' . ($purchase->ref_no ?? $id) . '.pdf');
            }
            
            // Fallback: return the print view
            return view('custom-print.purchase', compact('purchase', 'totals'));
            
        } catch (\Exception $e) {
            \Log::error('PDF Download Error: ' . $e->getMessage());
            abort(404, 'Purchase not found or PDF generation failed');
        }
    }
}