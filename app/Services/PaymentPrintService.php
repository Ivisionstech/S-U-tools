<?php

namespace App\Services;

use App\TransactionPayment;
use App\Transaction;
use App\Contact;
use App\Business;
use App\BusinessLocation;

class PaymentPrintService
{
    /**
     * Get payment data with all relations
     */
    public static function getPaymentData($id)
    {
        $payment = TransactionPayment::with([
            'transaction',
            'transaction.contact',
            'transaction.business',
            'transaction.location',
            'payment_account',
            'created_user'
        ])->findOrFail($id);

        // Get the transaction
        $transaction = $payment->transaction;
        
        // Add additional properties
        $payment->ref_no = $payment->payment_ref_no ?? 'N/A';
        $payment->paid_on = $payment->paid_on ?? $payment->created_at;
        $payment->amount = $payment->amount ?? 0;
        $payment->method = $payment->method ?? '';
        $payment->note = $payment->note ?? '';
        $payment->currency_symbol = session('currency')['symbol'] ?? 'PKR';

        return $payment;
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount)
    {
        return number_format((float) $amount, 0);
    }

    /**
     * Convert number to words
     */
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
            'other' => 'Other',
            'credit' => 'Credit',
            'debit' => 'Debit',
            'bank' => 'Bank',
            'mobile' => 'Mobile Payment',
            'custom_pay_1' => 'Custom Pay 1',
            'custom_pay_2' => 'Custom Pay 2',
            'custom_pay_3' => 'Custom Pay 3',
            'advance' => 'Advance'
        ];
        return $methods[$method] ?? ucfirst($method);
    }

    /**
     * Get payment type label
     */
    public static function getPaymentType($type)
    {
        $types = [
            'sell' => 'Sale',
            'purchase' => 'Purchase',
            'sell_return' => 'Sale Return',
            'purchase_return' => 'Purchase Return',
            'opening_balance' => 'Opening Balance',
            'expense' => 'Expense',
            'payroll' => 'Payroll'
        ];
        return $types[$type] ?? ucfirst($type);
    }
}