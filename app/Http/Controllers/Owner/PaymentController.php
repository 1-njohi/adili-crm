<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Payment;
use App\Services\PaymentAllocationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts/payments', 'public');
        }

        // Create payment
        $payment = Payment::create([
            'sale_id' => $sale->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'receipt_path' => $receiptPath,
            'notes' => $validated['notes'] ?? null,
            'allocated_to' => 'installment',
        ]);

        // Allocate payment to installments
        $allocationService = app(PaymentAllocationService::class);
        $allocationService->allocate($payment);

        return redirect()->back()->with('success', 'Payment recorded and allocated successfully.');
    }
}