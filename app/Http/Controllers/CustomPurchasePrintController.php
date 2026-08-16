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
        // ADD THIS DEBUG LINE
        \Log::info('CustomPrintController::print called with ID: ' . $id);
        
        try {
            // ADD THIS DEBUG LINE
            \Log::info('Attempting to get purchase data...');
            
            $purchase = PurchasePrintService::getPurchaseData($id);
            
            // ADD THIS DEBUG LINE
            \Log::info('Purchase data retrieved successfully');
            \Log::info('Purchase ref_no: ' . ($purchase->ref_no ?? 'null'));
            
            $totals = PurchasePrintService::calculateTotals($purchase);
            
            // ADD THIS DEBUG LINE
            \Log::info('Totals calculated: ' . json_encode($totals));
            
            return view('custom-print.purchase', compact('purchase', 'totals'));
            
        } catch (\Exception $e) {
            // ADD THIS DEBUG LINE
            \Log::error('Custom Print Error: ' . $e->getMessage());
            \Log::error('Error Trace: ' . $e->getTraceAsString());
            
            // Return the error message so you can see it
            return "Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine();
        }
    }

    public function preview($id)
    {
        return $this->print($id);
    }

    public function downloadPdf($id)
    {
        try {
            $purchase = PurchasePrintService::getPurchaseData($id);
            $totals = PurchasePrintService::calculateTotals($purchase);
            
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('custom-print.purchase-pdf', compact('purchase', 'totals'));
                return $pdf->download('purchase-' . ($purchase->ref_no ?? $id) . '.pdf');
            }
            
            return view('custom-print.purchase', compact('purchase', 'totals'));
            
        } catch (\Exception $e) {
            \Log::error('PDF Download Error: ' . $e->getMessage());
            return "PDF Error: " . $e->getMessage();
        }
    }
}