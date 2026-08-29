<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalController extends Controller
{
    public function index()
    {
        $buyerId = auth()->id();

        // Get all sales for the buyer with installments sorted
        $sales = Sale::where('buyer_id', $buyerId)
            ->with([
                'plot.project',
                'installments' => function ($query) {
                    $query->orderBy('installment_number', 'asc');
                },
                'payments'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($sales->isEmpty()) {
            return Inertia::render('buyer/Portal', [
                'hasSales' => false,
                'message' => 'You don\'t have any plot purchases yet.',
            ]);
        }

        $salesData = $sales->map(function ($sale) {
            $totalPaid = $sale->payments->sum('amount');
            $remaining = $sale->total_price - $totalPaid;
            $progress = $sale->total_price > 0 ? round(($totalPaid / $sale->total_price) * 100, 2) : 0;

            $nextInstallment = $sale->installments
                ->where('status', 'pending')
                ->where('due_date', '>=', now())
                ->sortBy('due_date')
                ->first();

            $overdueInstallments = $sale->installments
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count();

            return [
                'sale' => $sale,
                'total_paid' => $totalPaid,
                'remaining_balance' => max(0, $remaining),
                'progress' => $progress,
                'nextInstallment' => $nextInstallment,
                'overdueCount' => $overdueInstallments,
                'installments' => $sale->installments->map(function ($installment) {
                    return [
                        'id' => $installment->id,
                        'number' => $installment->installment_number,
                        'amount' => $installment->amount,
                        'paid_amount' => $installment->paid_amount,
                        'remaining_amount' => $installment->remaining_amount ?? ($installment->amount - $installment->paid_amount),
                        'due_date' => $installment->due_date->format('Y-m-d'),
                        'status' => $installment->status,
                        'paid_date' => $installment->paid_date ? $installment->paid_date->format('Y-m-d') : null,
                    ];
                }),
                'payments' => $sale->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'method' => $payment->method,
                        'allocated_to' => $payment->allocated_to,
                        'created_at' => $payment->created_at->format('Y-m-d H:i'),
                        'receipt_path' => $payment->receipt_path,
                    ];
                }),
                'deposit_receipt_path' => $sale->deposit_receipt_path,
                'plot' => $sale->plot,
                'project' => $sale->project,
            ];
        });

        return Inertia::render('buyer/Portal', [
            'hasSales' => true,
            'salesData' => $salesData,
        ]);
    }
}