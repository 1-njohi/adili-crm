<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Mail\PaymentRecorded;
use App\Models\Payment;
use App\Models\Sale;
use App\Services\PaymentAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $payment = Payment::create([
            'sale_id' => $sale->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'receipt_path' => $receiptPath,
            'notes' => $validated['notes'] ?? null,
            'allocated_to' => 'installment',
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
        ]);

        // Allocate to installments
        app(PaymentAllocationService::class)->allocate($payment);

        // ✅ Send email to buyer with receipt attached
        if ($sale->buyer && $sale->buyer->email) {
            Mail::to($sale->buyer->email)->send(new PaymentRecorded($payment));
        }

        return redirect()->back()->with('success', 'Payment recorded and buyer notified.');
    }
}
