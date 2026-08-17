<?php

namespace App\Http\Controllers;

use App\Services\PaymentPrintService;
use Illuminate\Http\Request;

class CustomPaymentPrintController extends Controller
{
    public function print($id)
    {
        try {
            $payment = PaymentPrintService::getPaymentData($id);
            return view('custom-print.payment', compact('payment'));
        } catch (\Exception $e) {
            \Log::error('Custom Payment Print Error: ' . $e->getMessage());
            
            // Return a user-friendly error page instead of a 404
            return view('custom-print.error', [
                'message' => 'Payment not found. Please try again with a valid payment.'
            ]);
        }
    }

    public function preview($id)
    {
        return $this->print($id);
    }

    public function downloadPdf($id)
    {
        try {
            $payment = PaymentPrintService::getPaymentData($id);
            
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('custom-print.payment-pdf', compact('payment'));
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download('payment-' . ($payment->ref_no ?? $id) . '.pdf');
            }
            
            return view('custom-print.payment', compact('payment'));
            
        } catch (\Exception $e) {
            \Log::error('PDF Download Error: ' . $e->getMessage());
            return view('custom-print.error', [
                'message' => 'Unable to generate PDF. Please try again.'
            ]);
        }
    }
}