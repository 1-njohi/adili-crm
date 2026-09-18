<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PlotController extends Controller
{
    public function show(Project $project, Plot $plot)
    {
        if ($plot->project_id !== $project->id) {
            abort(404);
        }

        $plot->refresh();
        $plot->load([
            'sale' => function ($query) {
                $query->with([
                    'buyer',
                    'agent',
                    'installments' => function ($q) {
                        $q->orderBy('installment_number', 'asc');
                    },
                    'payments',
                ]);
            },
        ]);

        $financialSummary = null;
        if ($plot->sale) {
            $sale = $plot->sale;
            $totalPaid = $sale->payments->sum('amount');
            $remaining = $sale->total_price - $totalPaid;

            $financialSummary = [
                'sale_id' => $sale->id,
                'total_price' => $sale->total_price,
                'deposit' => $sale->deposit,
                'total_paid' => $totalPaid,
                'remaining_balance' => max(0, $remaining),
                'progress_percentage' => $sale->total_price > 0 ? round(($totalPaid / $sale->total_price) * 100, 2) : 0,
                'installments_paid' => $sale->installments->where('status', 'paid')->count(),
                'installments_total' => $sale->installments->count(),
                'buyer' => $sale->buyer ? [
                    'id' => $sale->buyer->id,
                    'name' => $sale->buyer->name,
                    'email' => $sale->buyer->email,
                    'phone' => $sale->buyer->phone,
                ] : null,
                'agent' => $sale->agent ? [
                    'id' => $sale->agent->id,
                    'name' => $sale->agent->name,
                    'email' => $sale->agent->email,
                ] : null,
                'payment_tier_selected' => $sale->payment_tier_selected,
                'commission_rate' => $sale->commission_rate,
                'commission_type' => $sale->commission_type,
                'deposit_receipt_path' => $sale->deposit_receipt_path,
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
            ];
        }

        return Inertia::render('owner/plots/Show', [
            'project' => $project,
            'plot' => $plot,
            'financialSummary' => $financialSummary,
        ]);
    }

    public function create(Project $project)
    {
        return Inertia::render('owner/plots/Create', [
            'project' => $project,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'plot_number' => [
                'required',
                'integer',
                // 👇 This makes sure the plot_number is unique only within THIS project
                Rule::unique('plots', 'plot_number')->where('project_id', $project->id),
            ],
            'size' => 'nullable|string|max:255',
            'size_unit' => 'nullable|string|max:50',
            'payment_tiers' => 'nullable|array',
            'custom_attributes' => 'nullable|array',
            'status' => 'nullable|string|in:available,reserved,sold',
        ]);

        $plot = $project->plots()->create($validated);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Plot added successfully!');
    }

    public function edit(Project $project, Plot $plot)
    {
        return Inertia::render('owner/plots/Edit', [
            'project' => $project,
            'plot' => $plot,
        ]);
    }

    public function sell(Project $project, Plot $plot)
    {
        return Inertia::render('owner/plots/Sell', [
            'project' => $project,
            'plot' => $plot,
        ]);
    }

    public function update(Request $request, Project $project, Plot $plot)
    {
        $validated = $request->validate([
            'plot_number' => [
                'required',
                'integer',
                // 👇 Ignore the current plot, but still scope to the project
                Rule::unique('plots', 'plot_number')
                    ->where('project_id', $project->id)
                    ->ignore($plot->id),
            ],
            'size' => 'nullable|string|max:255',
            'size_unit' => 'nullable|string|max:50',
            'payment_tiers' => 'nullable|array',
            'custom_attributes' => 'nullable|array',
            'status' => 'nullable|string|in:available,reserved,sold',
        ]);

        $plot->update($validated);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Plot updated successfully!');
    }

    public function destroy(Project $project, Plot $plot)
    {
        $plot->delete();

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Plot deleted successfully!');
    }
}
