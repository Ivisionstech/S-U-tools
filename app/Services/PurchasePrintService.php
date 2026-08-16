<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Transaction;

class PurchasePrintService
{
    /**
     * Get purchase data with all relations
     */
    public static function getPurchaseData($id)
    {
        return Purchase::with([
            'contact',
            'business',
            'location',
            'purchase_lines',
            'purchase_lines.product',
            'purchase_lines.variations',
            'purchase_lines.product.unit',
            'payment_lines',
            'createdBy'
        ])->findOrFail($id);
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount)
    {
        return number_format($amount, 2);
    }

    /**
     * Convert number to words
     */
    public static function numberToWords($number)
    {
        try {
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            return ucwords($formatter->format($number));
        } catch (\Exception $e) {
            return $number;
        }
    }

    /**
     * Get payment method label
     */
    public static function getPaymentMethod($method)
    {
        $methods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other'
        ];
        return $methods[$method] ?? $method;
    }
}