<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function create(Project $project)
    {
        return Inertia::render('owner/expenses/Create', [
            'project' => $project,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'incurred_at' => 'nullable|date',
            'type' => 'nullable|string|in:pre_launch,post_launch',
            'internal_notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:2048', // Max 2MB
        ]);

        $validated['created_by'] = auth()->id();

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt_path'] = $path;
        }

        $expense = $project->expenses()->create($validated);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Expense added successfully!');
    }

    public function edit(Project $project, Expense $expense)
    {
        return Inertia::render('owner/expenses/Edit', [
            'project' => $project,
            'expense' => $expense,
        ]);
    }

    public function update(Request $request, Project $project, Expense $expense)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'incurred_at' => 'nullable|date',
            'type' => 'nullable|string|in:pre_launch,post_launch',
            'internal_notes' => 'nullable|string',
            'receipt' => 'nullable|file|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt_path'] = $path;
        }

        $expense->update($validated);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Project $project, Expense $expense)
    {
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Expense deleted successfully!');
    }
}