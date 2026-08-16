<?php

namespace App\Http\Controllers;

use App\Services\SellPrintService;
use Illuminate\Http\Request;

class CustomSellPrintController extends Controller
{
    public function print($id)
    {
        try {
            $sell = SellPrintService::getSellData($id);
            $totals = SellPrintService::calculateTotals($sell);
            return view('custom-print.sell', compact('sell', 'totals'));
        } catch (\Exception $e) {
            // Show the actual error for debugging
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
            $sell = SellPrintService::getSellData($id);
            $totals = SellPrintService::calculateTotals($sell);
            
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('custom-print.sell-pdf', compact('sell', 'totals'));
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download('invoice-' . ($sell->ref_no ?? $id) . '.pdf');
            }
            
            return view('custom-print.sell', compact('sell', 'totals'));
            
        } catch (\Exception $e) {
            return "PDF Error: " . $e->getMessage();
        }
    }
}