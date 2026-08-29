<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Installment;

class PaymentAllocationService
{
    public function allocate(Payment $payment)
    {
        // Skip deposit payments
        if ($payment->allocated_to === 'deposit') {
            return;
        }

        $sale = $payment->sale;
        $remainingAmount = $payment->amount;

        // Get unpaid/partial installments ordered by due_date
        $installments = $sale->installments()
            ->where('remaining_amount', '>', 0)
            ->orderBy('due_date')
            ->get();

        foreach ($installments as $installment) {
            if ($remainingAmount <= 0) {
                break;
            }

            $needed = $installment->remaining_amount;

            if ($remainingAmount >= $needed) {
                // Fully pay this installment
                $installment->update([
                    'paid_amount' => $installment->amount,
                    'remaining_amount' => 0,
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);
                $remainingAmount -= $needed;
            } else {
                // Partial payment
                $newPaidAmount = $installment->paid_amount + $remainingAmount;
                $newRemaining = $installment->remaining_amount - $remainingAmount;
                $installment->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => $newRemaining,
                    'status' => $newRemaining > 0 ? 'partial' : 'paid',
                    'paid_date' => $newRemaining <= 0 ? now() : null,
                ]);
                $remainingAmount = 0;
            }
        }

        // Update sale remaining balance
        $totalPaid = $sale->payments()->sum('amount');
        $remainingBalance = max(0, $sale->total_price - $totalPaid);

        $sale->update([
            'remaining_balance' => $remainingBalance,
        ]);

        // ✅ Mark sale as completed if fully paid
        if ($remainingBalance <= 0) {
            $sale->update([
                'status' => 'completed',
            ]);

            // Optionally: Update plot status to sold if not already
            if ($sale->plot) {
                $sale->plot->update(['status' => 'sold']);
            }

            // Optionally: Trigger commission release
            // event(new SaleCompleted($sale));
        }

        if ($sale->isFullyPaid()) {
            $sale->markAsCompleted();
        }
    }
}