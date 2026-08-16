<?php

namespace App\Services;

use App\Transaction;
use App\Contact;
use App\Business;
use App\BusinessLocation;
use App\PurchaseLine;
use App\TransactionPayment;

class PurchasePrintService
{
    /**
     * Get purchase data with all relations
     */
    public static function getPurchaseData($id)
    {
        $purchase = Transaction::with([
            'contact',
            'business',
            'location',
            'purchase_lines',
            'purchase_lines.product',
            'purchase_lines.variations',
            'payment_lines'
        ])->where('type', 'purchase')
          ->findOrFail($id);

        $purchase->ref_no = $purchase->ref_no ?? $purchase->invoice_no ?? 'N/A';
        $purchase->transaction_date = $purchase->transaction_date ?? $purchase->created_at;
        $purchase->final_total = $purchase->final_total ?? $purchase->total ?? 0;
        $purchase->purchase_tax = $purchase->tax_amount ?? 0;
        $purchase->shipping_charges = $purchase->shipping_charges ?? 0;
        $purchase->discount_type = $purchase->discount_type ?? 'fixed';
        $purchase->discount_amount = $purchase->discount_amount ?? 0;
        $purchase->status = $purchase->status ?? 'received';
        $purchase->payment_status = $purchase->payment_status ?? 'due';
        $purchase->additional_notes = $purchase->additional_notes ?? '';
        $purchase->currency_symbol = session('currency')['symbol'] ?? 'PKR';

        return $purchase;
    }

    public static function formatCurrency($amount)
    {
        return number_format((float) $amount, 0);
    }

    public static function numberToWords($number)
    {
        try {
            if (extension_loaded('intl') && class_exists('\NumberFormatter')) {
                $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                return ucwords($formatter->format((float) $number));
            }
            return (string) $number;
        } catch (\Exception $e) {
            return (string) $number;
        }
    }

    public static function getPaymentMethod($method)
    {
        $methods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other',
            'credit' => 'Credit',
            'debit' => 'Debit',
            'bank' => 'Bank',
            'mobile' => 'Mobile Payment'
        ];
        return $methods[$method] ?? ucfirst($method);
    }

    public static function getStatusColor($status)
    {
        $colors = [
            'received' => '#28a745',
            'pending' => '#ffc107',
            'ordered' => '#17a2b8',
            'draft' => '#6c757d',
            'completed' => '#28a745',
            'partial' => '#ffc107',
            'paid' => '#28a745',
            'due' => '#dc3545',
            'overdue' => '#dc3545'
        ];
        return $colors[$status] ?? '#6c757d';
    }

    public static function calculateTotals($purchase)
    {
        $totalBeforeTax = 0;
        if (!empty($purchase->purchase_lines)) {
            foreach ($purchase->purchase_lines as $line) {
                $totalBeforeTax += ((float) ($line->purchase_price ?? 0)) * ((float) ($line->quantity ?? 0));
            }
        }

        $discountAmount = 0;
        if (!empty($purchase->discount_type) && !empty($purchase->discount_amount)) {
            if ($purchase->discount_type == 'percentage') {
                $discountAmount = ($purchase->discount_amount * $totalBeforeTax) / 100;
            } else {
                $discountAmount = (float) $purchase->discount_amount;
            }
        }

        $taxAmount = (float) ($purchase->purchase_tax ?? 0);
        $shippingCharges = (float) ($purchase->shipping_charges ?? 0);
        $finalTotal = (float) ($purchase->final_total ?? 0);

        return [
            'total_before_tax' => $totalBeforeTax,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'shipping_charges' => $shippingCharges,
            'final_total' => $finalTotal,
            'total_paid' => self::calculateTotalPaid($purchase)
        ];
    }

    public static function calculateTotalPaid($purchase)
    {
        $total = 0;
        if (!empty($purchase->payment_lines)) {
            foreach ($purchase->payment_lines as $payment) {
                $total += (float) $payment->amount;
            }
        }
        return $total;
    }
}