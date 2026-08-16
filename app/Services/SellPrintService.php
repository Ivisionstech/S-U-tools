<?php

namespace App\Services;

use App\Transaction;
use App\Contact;
use App\Business;
use App\BusinessLocation;
use App\TransactionSellLine;
use App\TransactionPayment;

class SellPrintService
{
    /**
     * Get sell data with all relations
     */
    public static function getSellData($id)
    {
        // REMOVE 'createdBy' from this array
        $sell = Transaction::with([
            'contact',
            'business',
            'location',
            'sell_lines',
            'sell_lines.product',
            'sell_lines.variations',
            'payment_lines'
            // 'createdBy' - REMOVED
        ])->where('type', 'sell')
          ->findOrFail($id);

        $sell->ref_no = $sell->ref_no ?? $sell->invoice_no ?? 'N/A';
        $sell->transaction_date = $sell->transaction_date ?? $sell->created_at;
        $sell->final_total = $sell->final_total ?? $sell->total ?? 0;
        $sell->sell_tax = $sell->tax_amount ?? 0;
        $sell->shipping_charges = $sell->shipping_charges ?? 0;
        $sell->discount_type = $sell->discount_type ?? 'fixed';
        $sell->discount_amount = $sell->discount_amount ?? 0;
        $sell->status = $sell->status ?? 'completed';
        $sell->payment_status = $sell->payment_status ?? 'due';
        $sell->additional_notes = $sell->additional_notes ?? '';
        $sell->currency_symbol = session('currency')['symbol'] ?? 'PKR';

        return $sell;
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
            'completed' => '#28a745',
            'pending' => '#ffc107',
            'draft' => '#6c757d',
            'partial' => '#ffc107',
            'paid' => '#28a745',
            'due' => '#dc3545',
            'overdue' => '#dc3545'
        ];
        return $colors[$status] ?? '#6c757d';
    }

    public static function calculateTotals($sell)
    {
        $totalBeforeTax = 0;
        if (!empty($sell->sell_lines)) {
            foreach ($sell->sell_lines as $line) {
                $totalBeforeTax += ((float) ($line->unit_price ?? 0)) * ((float) ($line->quantity ?? 0));
            }
        }

        $discountAmount = 0;
        if (!empty($sell->discount_type) && !empty($sell->discount_amount)) {
            if ($sell->discount_type == 'percentage') {
                $discountAmount = ($sell->discount_amount * $totalBeforeTax) / 100;
            } else {
                $discountAmount = (float) $sell->discount_amount;
            }
        }

        $taxAmount = (float) ($sell->sell_tax ?? 0);
        $shippingCharges = (float) ($sell->shipping_charges ?? 0);
        $finalTotal = (float) ($sell->final_total ?? 0);

        return [
            'total_before_tax' => $totalBeforeTax,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'shipping_charges' => $shippingCharges,
            'final_total' => $finalTotal,
            'total_paid' => self::calculateTotalPaid($sell)
        ];
    }

    public static function calculateTotalPaid($sell)
    {
        $total = 0;
        if (!empty($sell->payment_lines)) {
            foreach ($sell->payment_lines as $payment) {
                $total += (float) $payment->amount;
            }
        }
        return $total;
    }
}